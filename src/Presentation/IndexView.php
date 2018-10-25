<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class IndexView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 */
	public function __construct(Twig_Environment $twig, BaseView $baseView)
	{
		$this->twig = $twig;
		$this->baseView = $baseView;
	}

	/**
	 * Show the home page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$content = $this->twig->render(
			'html/index.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Home',
			]
		);

		return new HtmlResponse($content);
	}
}
