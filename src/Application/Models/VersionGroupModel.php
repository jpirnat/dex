<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityFlagId;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveFlagId;
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
		private readonly VersionGroupRepositoryInterface $vgRepository,
	) {}


	/**
	 * Set the version group by its identifier.
	 */
	public function setByIdentifier(string $vgIdentifier) : VersionGroupId
	{
		$this->versionGroup = $this->vgRepository->getByIdentifier($vgIdentifier);

		return $this->versionGroup->getId();
	}

	/**
	 * Set the navigable version groups to all since the given version group.
	 */
	public function setSinceGeneration(GenerationId $generationId) : void
	{
		$this->versionGroups = $this->vgRepository->getSinceGeneration($generationId);
	}

	/**
	 * Set the navigable version groups to those that have this Pokémon.
	 */
	public function setWithPokemon(PokemonId $pokemonId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithPokemon($pokemonId);
	}

	/**
	 * Set the navigable version groups to those that have this move.
	 */
	public function setWithMove(MoveId $moveId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithMove($moveId);
	}

	/**
	 * Set the navigable version groups to those that have this move flag.
	 */
	public function setWithMoveFlag(MoveFlagId $flagId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithMoveFlag($flagId);
	}

	/**
	 * Set the navigable version groups to those that have this type.
	 */
	public function setWithType(TypeId $typeId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithType($typeId);
	}

	/**
	 * Set the navigable version groups to those that have this item.
	 */
	public function setWithItem(ItemId $itemId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithItem($itemId);
	}

	/**
	 * Set the navigable version groups to those that have abilities.
	 */
	public function setWithAbilities() : void
	{
		$this->versionGroups = $this->vgRepository->getWithAbilities();
	}

	/**
	 * Set the navigable version groups to those that have this ability.
	 */
	public function setWithAbility(AbilityId $abilityId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithAbility($abilityId);
	}

	/**
	 * Set the navigable version groups to those that have this ability flag.
	 */
	public function setWithAbilityFlag(AbilityFlagId $flagId) : void
	{
		$this->versionGroups = $this->vgRepository->getWithAbilityFlag($flagId);
	}

	/**
	 * Set the navigable version groups to those that have natures.
	 */
	public function setWithNatures() : void
	{
		$this->versionGroups = $this->vgRepository->getWithNatures();
	}

	/**
	 * Set the navigable version groups to those that have IVs.
	 */
	public function setWithIvs() : void
	{
		$this->versionGroups = $this->vgRepository->getWithIvs();
	}

	/**
	 * Set the navigable version groups to those that have EVs.
	 */
	public function setWithEvs() : void
	{
		$this->versionGroups = $this->vgRepository->getWithEvs();
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
