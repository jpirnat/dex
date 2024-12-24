<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexItemModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexItemView
{
	public function __construct(
		private DexItemModel $dexItemModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex item page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexItemModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$item = $this->dexItemModel->item;
		$evolutions = $this->dexItemModel->evolutions;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->identifier;
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/items",
			'text' => 'Items',
		], [
			'text' => $item['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Items - ' . $item['name'],

				'versionGroup' => [
					'id' => $versionGroup->id->value(),
					'identifier' => $versionGroup->identifier,
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'item' => $item,
				'evolutions' => $evolutions,
			]
		]);
	}
}
