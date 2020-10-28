<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Species
{
	public function __construct(
		private SpeciesId $id,
		private string $identifier,
		private VersionGroupId $introducedInVersionGroupId,
		private int $baseEggCycles,
	) {}

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
}
