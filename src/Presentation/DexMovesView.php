<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMovesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexMovesView
{
	public function __construct(
		private DexMovesModel $dexMovesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Show the dex moves page.
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
