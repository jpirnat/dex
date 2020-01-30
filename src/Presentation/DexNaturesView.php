<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexNaturesModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexNaturesView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private DexNaturesModel $dexNaturesModel;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexNaturesModel $dexNaturesModel
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexNaturesModel $dexNaturesModel
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->dexNaturesModel = $dexNaturesModel;
	}

	/**
	 * Show the dex natures page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Natures',
			],
		];

		$content = $this->renderer->render(
			'html/dex/natures.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Natures',
				'breadcrumbs' => $breadcrumbs,
				'natures' => $this->dexNaturesModel->getNatures(),
			]
		);

		return new HtmlResponse($content);
	}
}
