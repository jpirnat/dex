<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface DexTypeRepositoryInterface
{
	/**
	 * Get a dex type by its id.
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 */
	public function getById(
		TypeId $typeId,
		LanguageId $languageId
	) : DexType;

	/**
	 * Get the dex types of this Pokémon.
	 *
	 * @return DexType[] Ordered by Pokémon type slot.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array;

	/**
	 * Get the main dex types available in this generation.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getMainByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types available in this generation.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex types had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array;
}
