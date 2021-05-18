<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;

interface StatsUsagePokemonRepositoryInterface
{
	/**
	 * Get stats usage Pokémon by month, format, and rating.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsUsagePokemon[] Ordered by rank ascending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		GenerationId $generationId,
		LanguageId $languageId
	) : array;

	/**
	 * Get a stats usage Pokémon by month, format, rating, and Pokémon id.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return array|null
	 */
	public function getByPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		GenerationId $generationId,
		LanguageId $languageId
	) : ?array;

	/**
	 * Get a stats usage Pokémon by month, format, rating, and rank.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param int $rank
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return array|null
	 */
	public function getByRank(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		int $rank,
		GenerationId $generationId,
		LanguageId $languageId
	) : ?array;
}
