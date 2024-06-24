<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMovesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexMovesView
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
		$versionGroupModel = $this->dexMovesModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$showMoveDescriptions = $versionGroup->getId()->hasMoveDescriptions();
		$moves = $this->dexMovesModel->getMoves();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'text' => 'Moves',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'moves' => $this->dexFormatter->formatDexMoves($moves),
				'showMoveDescriptions' => $showMoveDescriptions,
			]
		]);
	}
}
