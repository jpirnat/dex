<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface BaseStatRepositoryInterface
{
	/**
	 * Get a Pokémon's base stats by version group and Pokémon.
	 */
	public function getByVersionGroupAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : StatValueContainer;

	/**
	 * Get all base stats had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
	) : array;

	/**
	 * Get all base stats had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array;

	/**
	 * Get all base stats had by Pokémon in this version group.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array;

	/**
	 * Get all base stats had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
	) : array;
}
