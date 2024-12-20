<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexEggGroupsModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexEggGroupsView
{
	public function __construct(
		private DexEggGroupsModel $dexEggGroupsModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex egg groups page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexEggGroupsModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$eggGroups = $this->dexEggGroupsModel->getEggGroups();
		$eggGroups = $this->dexFormatter->formatDexEggGroups($eggGroups);

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Egg Groups',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'eggGroups' => $eggGroups,
			]
		]);
	}
}
