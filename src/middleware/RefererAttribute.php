<?php

namespace Hiraeth\Middleware;

use Hiraeth;
use Exception;

use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use Psr\Http\Message\ServerRequestFactoryInterface as RequestFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * A middleware for creating a rich request object from the referer
 */
class RefererAttribute implements Middleware
{
	/**
	 * A request factory for creating a request from referer
	 *
	 * @var RequestFactory|null
	 */
	protected $factory = NULL;


	/**
	 * Create a new instance of the middleware
	 */
	public function __construct(RequestFactory $factory)
	{
		$this->factory = $factory;
	}


	/**
	 * {@inheritDoc}
	 */
	public function process(Request $request, Handler $handler): Response
	{
		$query = [];

		try {
			$referer = $this->factory->createServerRequest('GET', $request->getHeaderLine('Referer'));

			if ($referer->getUri()->getQuery()) {
				parse_str($referer->getUri()->getQuery(), $query);
			}

			$referer = $referer->withQueryParams($query);

		} catch (Exception $e) {
			$referer = NULL;
		}

		return $handler->handle($request->withAttribute('_referer', $referer));
	}
}
