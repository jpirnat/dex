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
			$this->baseView->getBaseVariables()
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex abilities page.
	 *
	 * @return ResponseInterface
	 */
	public function abilities() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/abilities.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Abilities',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex natures page.
	 *
	 * @return ResponseInterface
	 */
	public function natures() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/natures.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Natures',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex types page.
	 *
	 * @return ResponseInterface
	 */
	public function types() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/types.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Types',
			]
		);

		return new HtmlResponse($content);
	}
}
