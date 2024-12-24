<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTmsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexTmsView
{
	public function __construct(
		private DexTmsModel $dexTmsModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex TMs page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexTmsModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$machines = [];
		$m = $this->dexTmsModel->machines;
		foreach ($m as $machine) {
			$machines[] = [
				'item' => $this->dexFormatter->formatDexItem($machine['item']),
				'move' => $this->dexFormatter->formatDexMove($machine['move']),
			];
		}

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'TMs',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
					'hasItemIcons' => $versionGroup->id->hasItemIcons(),
					'hasMoveDescriptions' => $versionGroup->id->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'machines' => $machines,
			]
		]);
	}
}
