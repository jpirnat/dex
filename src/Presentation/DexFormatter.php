<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

class DexFormatter
{
	/**
	 * Transform an array of dex Pokémon objects into a renderable data array.
	 *
	 * @param DexPokemon[] $dexPokemon
	 *
	 * @return array
	 */
	public function formatDexPokemon(array $dexPokemons) : array
	{
		$pokemon = [];
		foreach ($dexPokemons as $dexPokemon) {
			$types = [];
			foreach ($dexPokemon->getTypes() as $dexPokemonType) {
				$types[] = [
					'identifier' => $dexPokemonType->getTypeIdentifier(),
					'icon' => $dexPokemonType->getTypeIcon(),
				];
			}

			$abilities = [];
			foreach ($dexPokemon->getAbilities() as $dexPokemonAbility) {
				$abilities[] = [
					'identifier' => $dexPokemonAbility->getAbilityIdentifier(),
					'name' => $dexPokemonAbility->getAbilityName(),
					'isHiddenAbility' => $dexPokemonAbility->isHiddenAbility(),
				];
			}

			$pokemon[] = [
				'formIcon' => $dexPokemon->getFormIcon(),
				'identifier' => $dexPokemon->getPokemonIdentifier(),
				'name' => $dexPokemon->getPokemonName(),
				'types' => $types,
				'abilities' => $abilities,
				'baseStats' => $dexPokemon->getBaseStats(),
			];
		}

		return $pokemon;
	}
}
