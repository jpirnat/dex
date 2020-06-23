<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

interface AbilityDescriptionRepositoryInterface
{
	/**
	 * Get an ability description by generation, language, and ability.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 *
	 * @return AbilityDescription
	 */
	public function getByGenerationAndLanguageAndAbility(
		GenerationId $generationId,
		LanguageId $languageId,
		AbilityId $abilityId
	) : AbilityDescription;
}
