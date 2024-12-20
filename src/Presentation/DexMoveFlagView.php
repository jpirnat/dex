<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMoveFlagModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexMoveFlagView
{
	public function __construct(
		private DexMoveFlagModel $dexMoveFlagModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex move flag page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexMoveFlagModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$flag = $this->dexMoveFlagModel->getFlag();
		$moves = $this->dexMoveFlagModel->getMoves();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/moves",
			'text' => 'Moves',
		], [
			'url' => "/dex/$vgIdentifier/moves#flags",
			'text' => 'Flags',
		], [
			'text' => $flag['name'],
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasMoveDescriptions' => $versionGroup->getId()->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'flag' => $flag,
				'moves' => $this->dexFormatter->formatDexMoves($moves),
			]
		]);
	}
}
