<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ErrorModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class ErrorView
{
	/** @var Twig_Environment $twig */
	protected $twig;

	/** @var ErrorModel $errorModel */
	protected $errorModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param ErrorModel $errorModel
	 */
	public function __construct(
		Twig_Environment $twig,
		ErrorModel $errorModel
	) {
		$this->twig = $twig;
		$this->errorModel = $errorModel;
	}

	/**
	 * Get the Error page.
	 *
	 * @return ResponseInterface
	 */
	public function getError() : ResponseInterface
	{
		$content = $this->twig->render('error.twig');

		$response = new Response();
		$response->getBody()->write($content);

		return $response;
	}
}
