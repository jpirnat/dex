<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\Structs\DexPokemon;
use Jp\Dex\Application\Models\Structs\DexPokemonAbility;
use Jp\Dex\Application\Models\Structs\DexPokemonType;

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
				'types' => $this->formatDexPokemonTypes($dexPokemon->getTypes()),
				'abilities' => $this->formatDexPokemonAbilities($dexPokemon->getAbilities()),
				'baseStats' => $dexPokemon->getBaseStats(),
			];
		}

		return $pokemon;
	}

	/**
	 * Transform an array of dex Pokémon type objects into a renderable data array.
	 *
	 * @param DexPokemonType[] $dexPokemonTypes
	 *
	 * @return array
	 */
	public function formatDexPokemonTypes(array $dexPokemonTypes) : array
	{
		$types = [];

		foreach ($dexPokemonTypes as $dexPokemonType) {
			$types[] = [
				'identifier' => $dexPokemonType->getTypeIdentifier(),
				'icon' => $dexPokemonType->getTypeIcon(),
			];
		}

		return $types;
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
}
