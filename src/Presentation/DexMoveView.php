<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMove\DexMoveModel;
use Jp\Dex\Application\Models\DexMove\DexMovePokemon;
use Jp\Dex\Application\Models\DexMove\DexMovePokemonMethod;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final class DexMoveView
{
	public function __construct(
		private DexMoveModel $dexMoveModel,
		private DexFormatter $dexFormatter,
	) {}

	/**
	 * Show the dex move page.
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexMoveModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$move = $this->dexMoveModel->getMove();
		$move = $this->dexFormatter->formatDexMove($move);
		$move += $this->dexMoveModel->getDetailedData();

		$types = $this->dexMoveModel->getTypes();
		$damageDealt = $this->dexMoveModel->getDamageDealt();

		$statChanges = $this->dexMoveModel->getStatChanges();
		$flags = $this->dexMoveModel->getFlags();

		$versionGroups = $this->dexMoveModel->getVersionGroups();
		$showAbilities = $generation->getId()->value() >= 3;

		$dexMovePokemonModel = $this->dexMoveModel->getDexMovePokemonModel();
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
		$generationIdentifier = $generation->getIdentifier();
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'url' => "/dex/$generationIdentifier/moves",
			'text' => 'Moves',
		], [
			'text' => $move['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Moves - ' . $move['name'],

				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),

				'move' => $move,
				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageDealt' => $damageDealt,
				'statChanges' => $statChanges,
				'flags' => $flags,

				'methods' => $this->formatDexMovePokemonMethods($methods),
				'versionGroups' => $this->dexFormatter->formatDexVersionGroups($versionGroups),
				'showAbilities' => $showAbilities,
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
