<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

interface DexPokemonRepositoryInterface
{
	/**
	 * Get a dex Pokémon by its id.
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : DexPokemon;

	/**
	 * Get all dex Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getWithAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return DexPokemon[] Indexed by Pokémon id.
	 */
	public function getWithMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get all dex Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getByType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array;
}
