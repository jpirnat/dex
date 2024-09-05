<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface DexAbilityRepositoryInterface
{
	/**
	 * Get the dex abilities available in this version group.
	 * This method is used to get data for the dex abilities page.
	 *
	 * @return array Ordered by ability name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get the dex abilities with this ability flag.
	 * This method is used to get data for the dex ability flag page.
	 *
	 * @return array Ordered by ability name.
	 */
	public function getByVgAndFlag(
		VersionGroupId $versionGroupId,
		AbilityFlagId $flagId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get the dex abilities of this Pokémon.
	 * This method is used to get data for the dex Pokémon page.
	 *
	 * @return array Ordered by slot.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array;
}
