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
	 * Show the dex ability page.
	 */
	public function index() : ResponseInterface
	{
		$versionGroupModel = $this->dexAbilityModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->getVersionGroup();
		$versionGroups = $versionGroupModel->getVersionGroups();

		$ability = $this->dexAbilityModel->getAbility();

		$stats = $this->dexAbilityModel->getStats();
		$normalPokemon = $this->dexAbilityModel->getNormalPokemon();
		$hiddenPokemon = $this->dexAbilityModel->getHiddenPokemon();

		$normalPokemon = $this->dexFormatter->formatDexPokemon($normalPokemon);
		$hiddenPokemon = $this->dexFormatter->formatDexPokemon($hiddenPokemon);

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
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
					'id' => $versionGroup->getId()->value(),
					'identifier' => $versionGroup->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'ability' => $ability,
				'pokemons' => array_merge($normalPokemon, $hiddenPokemon),
				'stats' => $stats,
			]
		]);
	}
}
