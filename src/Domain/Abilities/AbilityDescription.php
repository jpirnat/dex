<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class AbilityDescription
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private LanguageId $languageId,
		private AbilityId $abilityId,
		private string $description,
	) {}

	/**
	 * Get the ability description's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	/**
	 * Get the ability description's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the ability description's ability id.
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Get the ability description's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
