<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMovesModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexMovesView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private DexMovesModel $dexMovesModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexMovesModel $dexMovesModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexMovesModel $dexMovesModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->dexMovesModel = $dexMovesModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex moves page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexMovesModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$showMoveDescriptions = $generation->getId()->value() >= 3;
		$moves = $this->dexMovesModel->getMoves();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'text' => 'Moves',
		]];

		$content = $this->renderer->render(
			'html/dex/moves.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Moves',
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'showMoveDescriptions' => $showMoveDescriptions,
				'moves' => $this->dexFormatter->formatDexMoves($moves),
			]
		);

		return new HtmlResponse($content);
	}
}
