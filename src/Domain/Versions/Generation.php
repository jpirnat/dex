<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class Generation
{
	public function __construct(
		private GenerationId $id,
		private string $identifier,
		private string $smogonDexIdentifier,
	) {}

	/**
	 * Get the generation's id.
	 */
	public function getId() : GenerationId
	{
		return $this->id;
	}

	/**
	 * Get the generation's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation's Smogon dex identifier.
	 */
	public function getSmogonDexIdentifier() : string
	{
		return $this->smogonDexIdentifier;
	}
}
