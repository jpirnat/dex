<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;

interface AbilityDescriptionRepositoryInterface
{
	/**
	 * Get an ability description by generation, language, and ability.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param AbilityId $abilityId
	 *
	 * @throws AbilityDescriptionNotFoundException if no ability description
	 *     exists for this generation, language, and ability.
	 *
	 * @return AbilityDescription
	 */
	public function getByGenerationAndLanguageAndAbility(
		Generation $generation,
		LanguageId $languageId,
		AbilityId $abilityId
	) : AbilityDescription;

	/**
	 * Get ability descriptions by generation and language.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 *
	 * @return AbilityDescription[] Indexed by ability id.
	 */
	public function getByGenerationAndLanguage(
		Generation $generation,
		LanguageId $languageId
	) : array;
}
