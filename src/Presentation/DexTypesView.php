<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypesModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class DexTypesView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexTypesModel $dexTypesModel */
	private $dexTypesModel;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexTypesModel $dexTypesModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexTypesModel $dexTypesModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->dexTypesModel = $dexTypesModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex types page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexTypesModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$types = $this->dexTypesModel->getTypes();
		$factors = $this->dexTypesModel->getFactors();

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Types',
			],
		];

		$content = $this->renderer->render(
			'html/dex/types.twig',
			$this->baseView->getBaseVariables() + [
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'title' => 'Types',
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'types' => $this->dexFormatter->formatDexTypes($types),
				'factors' => $factors,
			]
		);

		return new HtmlResponse($content);
	}
}
