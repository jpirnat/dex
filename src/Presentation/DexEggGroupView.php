<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexEggGroupModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexEggGroupView
{
	public function __construct(
		private DexEggGroupModel $dexEggGroupModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex egg group page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexEggGroupModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$eggGroup = $this->dexEggGroupModel->getEggGroup();
		$stats = $this->dexEggGroupModel->getStats();
		$pokemon = $this->dexEggGroupModel->getPokemon();

		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/egg-groups",
			'text' => 'Egg Groups',
		], [
			'text' => $eggGroup['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Egg Groups - ' . $eggGroup['name'],

				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasAbilities' => $versionGroup->hasAbilities(),
					'hasBreeding' => $versionGroup->hasBreeding(),
					'hasEvYields' => $versionGroup->hasEvYields(),
					'hasEvBasedStats' => $versionGroup->hasEvBasedStats(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'eggGroup' => $eggGroup,
				'pokemons' => $pokemon,
				'stats' => $stats,
			]
		]);
	}
}
