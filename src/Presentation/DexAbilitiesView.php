<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexAbilitiesView
{
	public function __construct(
		private DexAbilitiesModel $dexAbilitiesModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex abilities page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexAbilitiesModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$abilities = $this->dexAbilitiesModel->getAbilities();

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'text' => 'Abilities',
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'abilities' => $abilities,
			]
		]);
	}
}
