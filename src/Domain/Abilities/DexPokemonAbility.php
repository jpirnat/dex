<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final class DexPokemonAbility
{
	/** @var string $identifier */
	private $identifier;

	/** @var string $name */
	private $name;

	/** @var bool $isHiddenAbility */
	private $isHiddenAbility;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param bool $isHiddenAbility
	 */
	public function __construct(
		string $identifier,
		string $name,
		bool $isHiddenAbility
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->isHiddenAbility = $isHiddenAbility;
	}

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
