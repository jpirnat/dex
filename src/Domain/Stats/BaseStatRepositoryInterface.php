<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

interface BaseStatRepositoryInterface
{
	/**
	 * Get a Pokémon's base stats by generation and Pokémon.
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : StatValueContainer;

	/**
	 * Get all base stats had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId
	) : array;

	/**
	 * Get all base stats had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId
	) : array;

	/**
	 * Get all base stats had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByGeneration(GenerationId $generationId) : array;

	/**
	 * Get all base stats had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId
	) : array;
}
