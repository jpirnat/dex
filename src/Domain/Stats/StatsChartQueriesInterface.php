<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;

interface StatsChartQueriesInterface
{
	/**
	 * Get the months that have data recorded for this format and rating.
	 *
	 * @return array Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMonthsWithData(FormatId $formatId, int $rating) : array;

	/**
	 * Get usage data for the usage chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsage(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : array;

	/**
	 * Get usage data for the lead usage chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getLeadUsage(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : array;

	/**
	 * Get usage data for the moveset ability chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId,
	) : array;

	/**
	 * Get usage data for the moveset item chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
	) : array;

	/**
	 * Get usage data for the moveset move chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
	) : array;

	/**
	 * Get usage data for the moveset Tera chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetTera(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		TypeId $typeId,
	) : array;

	/**
	 * Get usage data for the usage ability chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsageAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId,
	) : array;

	/**
	 * Get usage data for the usage item chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsageItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId,
	) : array;

	/**
	 * Get usage data for the usage move chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsageMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId,
	) : array;
}
