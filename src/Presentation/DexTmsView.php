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
		$versionGroupModel = $this->dexTmsModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$machines = [];
		$m = $this->dexTmsModel->getMachines();
		foreach ($m as $machine) {
			$machines[] = [
				'item' => $this->dexFormatter->formatDexItem($machine['item']),
				'move' => $this->dexFormatter->formatDexMove($machine['move']),
			];
		}

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'TMs',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasItemIcons' => $versionGroup->getId()->hasItemIcons(),
					'hasMoveDescriptions' => $versionGroup->getId()->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'machines' => $machines,
			]
		]);
	}
}
