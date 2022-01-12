<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class AbilityDescription
{
	public function __construct(
		private GenerationId $generationId,
		private LanguageId $languageId,
		private AbilityId $abilityId,
		private string $description,
	) {}

	/**
	 * Get the ability description's generation id.
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
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
