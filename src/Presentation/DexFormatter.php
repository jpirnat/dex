<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\Structs\DexMove;
use Jp\Dex\Application\Models\Structs\DexPokemon;
use Jp\Dex\Application\Models\Structs\DexPokemonAbility;
use Jp\Dex\Application\Models\Structs\DexType;

class DexFormatter
{
	/**
	 * Transform an array of dex Pokémon objects into a renderable data array.
	 *
	 * @param DexPokemon[] $dexPokemons
	 *
	 * @return array
	 */
	public function formatDexPokemon(array $dexPokemons) : array
	{
		$pokemon = [];

		foreach ($dexPokemons as $dexPokemon) {
			$pokemon[] = [
				'formIcon' => $dexPokemon->getFormIcon(),
				'identifier' => $dexPokemon->getPokemonIdentifier(),
				'name' => $dexPokemon->getPokemonName(),
				'types' => $this->formatDexTypes($dexPokemon->getTypes()),
				'abilities' => $this->formatDexPokemonAbilities($dexPokemon->getAbilities()),
				'baseStats' => $dexPokemon->getBaseStats(),
			];
		}

		return $pokemon;
	}

	/**
	 * Transform an array of dex type objects into a renderable data array.
	 *
	 * @param DexType[] $dexTypes
	 *
	 * @return array
	 */
	public function formatDexTypes(array $dexTypes) : array
	{
		$types = [];

		foreach ($dexTypes as $dexType) {
			$types[] = $this->formatDexType($dexType);
		}

		return $types;
	}

	/**
	 * Transform a dex type object into a renderable data array.
	 *
	 * @param DexType $dexType
	 *
	 * @return array
	 */
	public function formatDexType(DexType $dexType) : array
	{
		return [
			'identifier' => $dexType->getTypeIdentifier(),
			'icon' => $dexType->getTypeIcon(),
		];
	}

	/**
	 * Transform an array of dex Pokémon ability objects into a renderable data array.
	 *
	 * @param DexPokemonAbility[] $dexPokemonAbilities
	 *
	 * @return array
	 */
	public function formatDexPokemonAbilities(array $dexPokemonAbilities) : array
	{
		$abilities = [];

		foreach ($dexPokemonAbilities as $dexPokemonAbility) {
			$abilities[] = [
				'identifier' => $dexPokemonAbility->getAbilityIdentifier(),
				'name' => $dexPokemonAbility->getAbilityName(),
				'isHiddenAbility' => $dexPokemonAbility->isHiddenAbility(),
			];
		}

		return $abilities;
	}

	/**
	 * Transform an array of dex move objects into a renderable data array.
	 *
	 * @param DexMove[] $dexMoves
	 *
	 * @return array
	 */
	public function formatDexMoves(array $dexMoves) : array
	{
		$moves = [];

		foreach ($dexMoves as $dexMove) {
			$moves[] = [
				'identifier' => $dexMove->getMoveIdentifier(),
				'name' => $dexMove->getMoveName(),
				'type' => $this->formatDexType($dexMove->getType()),
				'categoryIcon' => $dexMove->getCategoryIcon(),
				'pp' => $dexMove->getPP(),
				'power' => $dexMove->getPower(),
				'accuracy' => $dexMove->getAccuracy(),
				'priority' => $dexMove->getPriority(),
				'description' => $dexMove->getMoveDescription(),
			];
		}

		return $moves;
	}
}
