<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class IndexView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 */
	public function __construct(Twig_Environment $twig)
	{
		$this->twig = $twig;
	}

	/**
	 * Show the home page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$content = $this->twig->render('html/index.twig', []);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
