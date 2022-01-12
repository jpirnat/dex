<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

interface DexPokemonAbilityRepositoryInterface
{
	/**
	 * Get the dex Pokémon abilities of this Pokémon.
	 *
	 * @return DexPokemonAbility[] Ordered by Pokémon ability slot.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays indexed by ability id and ordered by Pokémon ability slot.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon abilities had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array;
}
