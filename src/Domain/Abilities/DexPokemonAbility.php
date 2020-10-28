<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final class DexPokemonAbility
{
	public function __construct(
		private string $identifier,
		private string $name,
		private bool $isHiddenAbility,
	) {}

	/**
	 * Get the ability's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the ability's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Is this a hidden ability?
	 *
	 * @return bool
	 */
	public function isHiddenAbility() : bool
	{
		return $this->isHiddenAbility;
	}
}
