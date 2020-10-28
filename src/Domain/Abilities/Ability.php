<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Ability
{
	public function __construct(
		private AbilityId $id,
		private string $identifier,
		private VersionGroupId $introducedInVersionGroupId,
	) {}

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
