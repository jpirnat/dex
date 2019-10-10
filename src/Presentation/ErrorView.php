<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ErrorModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class ErrorView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var ErrorModel $errorModel */
	private $errorModel;

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
				'title' => 'An error has occurred!',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		return new HtmlResponse($content);
	}
}
