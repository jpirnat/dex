<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilityFlagModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexAbilityFlagView
{
	public function __construct(
		private DexAbilityFlagModel $dexAbilityFlagModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex ability flag page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexAbilityFlagModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$flag = $this->dexAbilityFlagModel->flag;
		$abilities = $this->dexAbilityFlagModel->abilities;

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/abilities",
			'text' => 'Abilities',
		], [
			'url' => "/dex/$vgIdentifier/abilities#flags",
			'text' => 'Flags',
		], [
			'text' => $flag['name'],
		]];

		return new JsonResponse([
			'data' => [
				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'flag' => $flag,
				'abilities' => $abilities,
			]
		]);
	}
}
