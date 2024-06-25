<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Species;

final readonly class Species
{
	public function __construct(
		private SpeciesId $id,
		private string $identifier,
		private int $baseEggCycles,
	) {}

	/**
	 * Get the species's id.
	 */
	public function getId() : SpeciesId
	{
		return $this->id;
	}

	/**
	 * Get the species's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the species's base egg cycles.
	 */
	public function getBaseEggCycles() : int
	{
		return $this->baseEggCycles;
	}
}
