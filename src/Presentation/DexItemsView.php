<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexItemsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexItemsView
{
	public function __construct(
		private DexItemsModel $dexItemsModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex items page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexItemsModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$items = $this->dexItemsModel->items;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Items',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
					'hasItemIcons' => $versionGroup->id->hasItemIcons(),
					'hasItemDescriptions' => $versionGroup->id->hasItemDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'items' => $this->dexFormatter->formatDexItems($items),
			]
		]);
	}
}
