<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilityModel;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexAbilityView
{
	public function __construct(
		private DexAbilityModel $dexAbilityModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex ability page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexAbilityModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$ability = $this->dexAbilityModel->getAbility();
		$flags = $this->dexAbilityModel->getFlags();
		$stats = $this->dexAbilityModel->getStats();
		$pokemon = $this->dexAbilityModel->getPokemon();

		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/abilities",
			'text' => 'Abilities',
		], [
			'text' => $ability['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Abilities - ' . $ability['name'],

				'versionGroup' => [
					'identifier' => $versionGroup->getIdentifier(),
					'hasAbilities' => $versionGroup->hasAbilities(),
					'hasBreeding' => $versionGroup->hasBreeding(),
					'hasEvYields' => $versionGroup->hasEvYields(),
					'hasEvBasedStats' => $versionGroup->hasEvBasedStats(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'ability' => $ability,
				'flags' => $flags,
				'pokemons' => $pokemon,
				'stats' => $stats,
			]
		]);
	}
}
