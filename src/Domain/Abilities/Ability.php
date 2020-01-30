<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Ability
{
	private AbilityId $id;
	private string $identifier;
	private VersionGroupId $introducedInVersionGroupId;

	/**
	 * Constructor.
	 *
	 * @param AbilityId $abilityId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 */
	public function __construct(
		AbilityId $abilityId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId
	) {
		$this->id = $abilityId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
	}

	/**
	 * Get the ability's id.
	 *
	 * @return AbilityId
	 */
	public function getId() : AbilityId
	{
		return $this->id;
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
	 * Get the version group id this ability was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}
}
