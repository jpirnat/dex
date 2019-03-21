<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexNaturesModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class DexNaturesView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexNaturesModel $dexNaturesModel */
	private $dexNaturesModel;

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
