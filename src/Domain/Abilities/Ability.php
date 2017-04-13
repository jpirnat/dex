<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Versions\Generation;

class Ability
{
	/** @var AbilityId $id */
	protected $id;

	/** @var string $identifier */
	protected $identifier;

	/** @var Generation $introducedInGeneration */
	protected $introducedInGeneration;

	/**
	 * Constructor.
	 *
	 * @param AbilityId $abilityId
	 * @param string $identifier
	 * @param Generation $introducedInGeneration
	 */
	public function __construct(
		AbilityId $abilityId,
		string $identifier,
		Generation $introducedInGeneration
	) {
		$this->id = $abilityId;
		$this->identifier = $identifier;
		$this->introducedInGeneration = $introducedInGeneration;
	}

	/**
	 * Get the ability's id.
	 *
	 * @return AbilityId
	 */
	public function id() : AbilityId
	{
		return $this->id;
	}

	/**
	 * Get the ability's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the generation this ability was introduced in.
	 *
	 * @return Generation
	 */
	public function introducedInGeneration() : Generation
	{
		return $this->introducedInGeneration;
	}
}
