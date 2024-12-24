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
	 * Get data for the dex moves page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexMovesModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$moves = $this->dexMovesModel->moves;
		$flags = $this->dexMovesModel->flags;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Moves',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
					'hasMoveDescriptions' => $versionGroup->id->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'moves' => $this->dexFormatter->formatDexMoves($moves),
				'flags' => $flags,
			]
		]);
	}
}
