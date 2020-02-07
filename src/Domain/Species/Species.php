<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Species
{
	private SpeciesId $id;
	private string $identifier;
	private VersionGroupId $introducedInVersionGroupId;
	private int $baseEggCycles;
	private int $baseFriendship;

	/**
	 * Constructor.
	 *
	 * @param SpeciesId $speciesId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 * @param int $baseEggCycles
	 * @param int $baseFriendship
	 */
	public function __construct(
		SpeciesId $speciesId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId,
		int $baseEggCycles,
		int $baseFriendship
	) {
		$this->id = $speciesId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
		$this->baseEggCycles = $baseEggCycles;
		$this->baseFriendship = $baseFriendship;
	}

	/**
	 * Get the species's id.
	 *
	 * @return SpeciesId
	 */
	public function getId() : SpeciesId
	{
		return $this->id;
	}

	/**
	 * Get the species's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group id this species was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}

	/**
	 * Get the species's base egg cycles.
	 *
	 * @return int
	 */
	public function getBaseEggCycles() : int
	{
		return $this->baseEggCycles;
	}

	/**
	 * Get the species's base friendship.
	 *
	 * @return int
	 */
	public function getBaseFriendship() : int
	{
		return $this->baseFriendship;
	}
}
