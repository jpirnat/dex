<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

class DexPokemonAbility
{
	/** @var string $abilityIdentifier */
	private $abilityIdentifier;

	/** @var string $abilityName */
	private $abilityName;

	/** @var bool $isHiddenAbility */
	private $isHiddenAbility;

	/**
	 * Constructor.
	 *
	 * @param string $abilityIdentifier
	 * @param string $abilityName
	 * @param bool $isHiddenAbility
	 */
	public function __construct(
		string $abilityIdentifier,
		string $abilityName,
		bool $isHiddenAbility
	) {
		$this->abilityIdentifier = $abilityIdentifier;
		$this->abilityName = $abilityName;
		$this->isHiddenAbility = $isHiddenAbility;
	}

	/**
	 * Get the ability identifier.
	 *
	 * @return string
	 */
	public function getAbilityIdentifier() : string
	{
		return $this->abilityIdentifier;
	}

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public function getAbilityName() : string
	{
		return $this->abilityName;
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
