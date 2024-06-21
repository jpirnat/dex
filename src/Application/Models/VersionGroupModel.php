<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

/**
 * This model is used to determine the version group being queried by the dex,
 * and also to get other version groups for navigation purposes.
 */
final class VersionGroupModel
{
	private VersionGroup $versionGroup;

	/** @var VersionGroup[] $versionGroups */
	private array $versionGroups = [];


	public function __construct(
		private VersionGroupRepositoryInterface $versionGroupRepository,
	) {}


	/**
	 * Set the version group by its identifier.
	 */
	public function setByIdentifier(string $vgIdentifier) : VersionGroupId
	{
		$this->versionGroup = $this->versionGroupRepository->getByIdentifier(
			$vgIdentifier
		);

		return $this->versionGroup->getId();
	}

	/**
	 * Set the navigable version groups to all since the given version group.
	 */
	public function setSinceGeneration(GenerationId $generationId) : void
	{
		$this->versionGroups = $this->versionGroupRepository->getSinceGeneration($generationId);
	}

	/**
	 * Set the navigable version groups to those that have this Pokémon.
	 */
	public function setWithPokemon(PokemonId $pokemonId) : void
	{
		$this->versionGroups = $this->versionGroupRepository->getWithPokemon($pokemonId);
	}

	/**
	 * Set the navigable version groups to those that have this move.
	 */
	public function setWithMove(MoveId $moveId) : void
	{
		$this->versionGroups = $this->versionGroupRepository->getWithMove($moveId);
	}

	/**
	 * Set the navigable version groups to those that have this move.
	 */
	public function setWithType(TypeId $typeId) : void
	{
		$this->versionGroups = $this->versionGroupRepository->getWithType($typeId);
	}

	/**
	 * Set the navigable version groups to those that have this ability.
	 */
	public function setWithAbility(AbilityId $abilityId) : void
	{
		$this->versionGroups = $this->versionGroupRepository->getWithAbility($abilityId);
	}


	/**
	 * Get the version group.
	 */
	public function getVersionGroup() : VersionGroup
	{
		return $this->versionGroup;
	}

	/**
	 * Get the version groups.
	 *
	 * @return VersionGroup[]
	 */
	public function getVersionGroups() : array
	{
		return $this->versionGroups;
	}
}
