<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMove\DexMoveModel;
use Jp\Dex\Application\Models\DexMove\DexMovePokemon;
use Jp\Dex\Application\Models\DexMove\DexMovePokemonMethod;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexMoveView
{
	public function __construct(
		private DexMoveModel $dexMoveModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Get data for the dex move page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexMoveModel->getVersionGroupModel();
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$move = $this->dexMoveModel->getMove();
		$move = $this->dexFormatter->formatDexMove($move);
		$move += $this->dexMoveModel->getDetailedData();

		$types = $this->dexMoveModel->getTypes();
		$damageDealt = $this->dexMoveModel->getDamageDealt();

		$statChanges = $this->dexMoveModel->getStatChanges();
		$flags = $this->dexMoveModel->getFlags();

		$dexMovePokemonModel = $this->dexMoveModel->getDexMovePokemonModel();
		$learnsetVgs = $dexMovePokemonModel->getLearnsetVgs();
		$stats = $dexMovePokemonModel->getStats();
		$methods = $dexMovePokemonModel->getMethods();

		// Sort Pokémon within each move method.
		$bySort = function (DexMovePokemon $a, DexMovePokemon $b) : int {
			return $a->getSort() <=> $b->getSort();
		};
		foreach ($methods as $method) {
			uasort($method->getPokemon(), $bySort);
		}

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/moves",
			'text' => 'Moves',
		], [
			'text' => $move['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Moves - ' . $move['name'],

				'versionGroup' => [
					'id' => $versionGroup->getId()->value(),
					'identifier' => $versionGroup->getIdentifier(),
					'generationId' => $versionGroup->getGenerationId()->value(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'move' => $move,
				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageDealt' => $damageDealt,
				'statChanges' => $statChanges,
				'flags' => $flags,

				'methods' => $this->formatDexMovePokemonMethods($methods),
				'learnsetVgs' => $this->dexFormatter->formatDexVersionGroups($learnsetVgs),
				'showAbilities' => $versionGroup->getId()->hasAbilities(),
				'stats' => $stats,
			]
		]);
	}

	/**
	 * Transform an array of dex move Pokémon method objects into a renderable
	 * data array.
	 *
	 * @param DexMovePokemonMethod[] $dexMovePokemonMethods
	 */
	private function formatDexMovePokemonMethods(array $dexMovePokemonMethods) : array
	{
		$methods = [];

		foreach ($dexMovePokemonMethods as $method) {
			$methods[] = [
				'identifier' => $method->getIdentifier(),
				'name' => $method->getName(),
				'description' => $method->getDescription(),
				'pokemons' => $this->formatDexMovePokemon($method->getPokemon()),
			];
		}

		return $methods;
	}

	/**
	 * Transform an array of dex move Pokémon objects into a renderable data
	 * array.
	 *
	 * @param DexMovePokemon[] $dexMovePokemon
	 */
	private function formatDexMovePokemon(array $dexMovePokemon) : array
	{
		$pokemons = [];

		foreach ($dexMovePokemon as $pokemon) {
			$pokemons[] = [
				'vgData' => $pokemon->getVersionGroupData(),
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'types' => $this->dexFormatter->formatDexTypes($pokemon->getTypes()),
				'abilities' => $this->dexFormatter->formatDexPokemonAbilities(
					$pokemon->getAbilities()
				),
				'baseStats' => $pokemon->getBaseStats(),
				'bst' => $pokemon->getBaseStatTotal(),
				'sort' => $pokemon->getSort(),
			];
		}

		return $pokemons;
	}
}
