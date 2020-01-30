<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypesModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexTypesView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private DexTypesModel $dexTypesModel;
	private DexFormatter $dexFormatter;

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
