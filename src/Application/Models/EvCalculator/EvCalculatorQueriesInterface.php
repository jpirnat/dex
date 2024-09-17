<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\EvCalculator;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface EvCalculatorQueriesInterface
{
	/**
	 * Get Pokémon for the EV calculator page.
	 */
	public function getPokemons(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get natures for the EV calculator page.
	 */
	public function getNatures(LanguageId $languageId) : array;

	/**
	 * Get stats for the EV calculator page.
	 */
	public function getStats(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

}
