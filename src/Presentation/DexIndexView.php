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
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasAbilities' => $versionGroup->hasAbilities(),
					'hasNatures' => $versionGroup->hasNatures(),
					'hasIvBasedStats' => $versionGroup->hasIvBasedStats(),
					'hasEvBasedStats' => $versionGroup->hasEvBasedStats(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),
			]
		]);
	}
}
