<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexTypesView
{
	public function __construct(
		private DexTypesModel $dexTypesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex types page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexTypesModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$types = $this->dexTypesModel->getTypes();
		$multipliers = $this->dexTypesModel->getMultipliers();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Types',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'types' => $types,
				'multipliers' => $multipliers,
			]
		]);
	}
}
