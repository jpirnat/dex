<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ErrorModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class ErrorView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private ErrorModel $errorModel;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param ErrorModel $errorModel
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		ErrorModel $errorModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->errorModel = $errorModel;
	}

	/**
	 * Get the Error page.
	 *
	 * @return ResponseInterface
	 */
	public function getError() : ResponseInterface
	{
		$breadcrumbs = [
			[
				'text' => '???',
			],
		];

		$content = $this->renderer->render(
			'html/error.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Error',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		return new HtmlResponse($content);
	}
}
