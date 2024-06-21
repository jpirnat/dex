<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;

interface VersionGroupRepositoryInterface
{
	/**
	 * Get a version group by its id.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 */
	public function getById(VersionGroupId $versionGroupId) : VersionGroup;

	/**
	 * Get a version group by its identifier.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 */
	public function getByIdentifier(string $identifier) : VersionGroup;

	/**
	 * Get version groups since this generation.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getSinceGeneration(GenerationId $generationId) : array;

	/**
	 * Get version groups that have this Pokémon.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithPokemon(PokemonId $pokemonId) : array;

	/**
	 * Get version groups that have this move.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithMove(MoveId $moveId) : array;

	/**
	 * Get version groups that have this type.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithType(TypeId $typeId) : array;

	/**
	 * Get version groups that have this ability.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbility(AbilityId $abilityId) : array;
}
