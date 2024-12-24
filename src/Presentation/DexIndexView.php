<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexIndexModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexIndexView
{
	public function __construct(
		private DexIndexModel $dexIndexModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex index page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexIndexModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->identifier,
					'hasAbilities' => $versionGroup->hasAbilities,
					'hasNatures' => $versionGroup->hasNatures,
					'hasTms' => $versionGroup->id->hasTms(),
					'hasBreeding' => $versionGroup->hasBreeding,
					'statFormulaType' => $versionGroup->statFormulaType,
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),
			]
		]);
	}
}
