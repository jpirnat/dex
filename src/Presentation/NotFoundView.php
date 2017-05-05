<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\NotFoundModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class NotFoundView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var NotFoundModel $notFoundModel */
	private $notFoundModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param NotFoundModel $notFoundModel
	 */
	public function __construct(
		Twig_Environment $twig,
		NotFoundModel $notFoundModel
	) {
		$this->twig = $twig;
		$this->notFoundModel = $notFoundModel;
	}

	/**
	 * Get the 404 Not Found page.
	 *
	 * @return ResponseInterface
	 */
	public function get404() : ResponseInterface
	{
		$content = $this->twig->render('404.twig');

		$response = new Response();
		$response->getBody()->write($content);

		return $response;
	}
}
