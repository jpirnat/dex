<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\NotFoundModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class NotFoundView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private NotFoundModel $notFoundModel;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param NotFoundModel $notFoundModel
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		NotFoundModel $notFoundModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->notFoundModel = $notFoundModel;
	}

	/**
	 * Get the 404 Not Found page.
	 *
	 * @return ResponseInterface
	 */
	public function get404() : ResponseInterface
	{
		$breadcrumbs = [
			[
				'text' => '???',
			],
		];

		$content = $this->renderer->render(
			'html/404.twig',
			$this->baseView->getBaseVariables() + [
				'title' => '404 Not Found',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		return new HtmlResponse($content);
	}
}
