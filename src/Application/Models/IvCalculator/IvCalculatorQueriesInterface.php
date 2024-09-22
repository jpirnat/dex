<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\IvCalculator;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface IvCalculatorQueriesInterface
{
	/**
	 * Get Pokémon for the IV calculator page.
	 */
	public function getPokemons(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get natures for the IV calculator page.
	 */
	public function getNatures(LanguageId $languageId) : array;

	/**
	 * Get characteristics for the IV calculator page.
	 */
	public function getCharacteristics(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get types for the IV calculator page.
	 */
	public function getTypes(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get stats for the IV calculator page.
	 */
	public function getStats(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

}
