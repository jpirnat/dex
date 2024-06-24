<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class AbilityName
{
	public function __construct(
		private LanguageId $languageId,
		private AbilityId $abilityId,
		private string $name,
	) {}

	/**
	 * Get the ability name's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the ability name's ability id.
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
	}

	/**
	 * Get the ability name's name value.
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
