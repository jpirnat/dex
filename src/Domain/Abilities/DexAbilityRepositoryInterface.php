<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface DexAbilityRepositoryInterface
{
	/**
	 * Get the dex abilities available in this generation. This method is used
	 * to get data for the dex abilities page.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return array Ordered by ability name.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get the dex abilities of this Pokémon. This method is used to get data
	 * for the dex Pokémon page.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return array Ordered by slot.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array;
}
