<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class DexPokemonAbility
{
	public function __construct(
		private string $identifier,
		private string $name,
		private bool $isHiddenAbility,
	) {}

	/**
	 * Get the ability's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the ability's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Is this a hidden ability?
	 */
	public function isHiddenAbility() : bool
	{
		return $this->isHiddenAbility;
	}
}
