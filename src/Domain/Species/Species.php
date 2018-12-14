<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

use Jp\Dex\Domain\Versions\VersionGroupId;

class Species
{
	/** @var SpeciesId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var VersionGroupId $introducedInVersionGroupId */
	private $introducedInVersionGroupId;

	/** @var int $baseEggCycles */
	private $baseEggCycles;

	/** @var int $baseFriendship */
	private $baseFriendship;

	/** @var ExperienceGroupId $experienceGroupId */
	private $experienceGroupId;

	/**
	 * Constructor.
	 *
	 * @param SpeciesId $speciesId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 * @param int $baseEggCycles
	 * @param int $baseFriendship
	 * @param ExperienceGroupId $experienceGroupId
	 */
	public function __construct(
		SpeciesId $speciesId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId,
		int $baseEggCycles,
		int $baseFriendship,
		ExperienceGroupId $experienceGroupId
	) {
		$this->id = $speciesId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
		$this->baseEggCycles = $baseEggCycles;
		$this->baseFriendship = $baseFriendship;
		$this->experienceGroupId = $experienceGroupId;
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

	/**
	 * Get the species's experience group id.
	 *
	 * @return ExperienceGroupId
	 */
	public function getExperienceGroupId() : ExperienceGroupId
	{
		return $this->experienceGroupId;
	}
}
