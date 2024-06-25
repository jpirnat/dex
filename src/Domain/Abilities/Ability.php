<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class Ability
{
	public function __construct(
		private AbilityId $id,
		private string $identifier,
	) {}

	/**
	 * Get the ability's id.
	 */
	public function getId() : AbilityId
	{
		return $this->id;
	}

	/**
	 * Get the ability's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}
}
