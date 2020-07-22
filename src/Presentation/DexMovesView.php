<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMovesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexMovesView
{
	private DexMovesModel $dexMovesModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param DexMovesModel $dexMovesModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		DexMovesModel $dexMovesModel,
		DexFormatter $dexFormatter
	) {
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

		return new JsonResponse([
			'data' => [
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),

				'moves' => $this->dexFormatter->formatDexMoves($moves),
				'showMoveDescriptions' => $showMoveDescriptions,
			]
		]);
	}
}
