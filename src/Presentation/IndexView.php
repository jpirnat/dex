<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class IndexView
{
	private RendererInterface $renderer;
	private BaseView $baseView;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 */
	public function __construct(RendererInterface $renderer, BaseView $baseView)
	{
		$this->renderer = $renderer;
		$this->baseView = $baseView;
	}

	/**
	 * Show the home page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/index.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Home',
			]
		);

		return new HtmlResponse($content);
	}
}
