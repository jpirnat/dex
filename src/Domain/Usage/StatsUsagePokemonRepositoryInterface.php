<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface StatsUsagePokemonRepositoryInterface
{
	/**
	 * Get stats usage Pokémon by month, format, and rating.
	 *
	 * @return StatsUsagePokemon[] Ordered by rank ascending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array;

	/**
	 * Get a stats usage Pokémon by month, format, rating, and Pokémon id.
	 */
	public function getByPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : ?array;

	/**
	 * Get a stats usage Pokémon by month, format, rating, and rank.
	 */
	public function getByRank(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		int $rank,
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : ?array;
}
