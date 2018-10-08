<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface RatingQueriesInterface
{
	/**
	 * Get the ratings for which usage data is available for this month and format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormat(DateTime $month, FormatId $formatId) : array;

	/**
	 * Get the ratings for which usage data is available for this month, format, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndPokemon(DateTime $month, FormatId $formatId, PokemonId $pokemonId) : array;

	/**
	 * Get the ratings for which usage data is available for this month, format, and ability.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param AbilityId $abilityId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndAbility(DateTime $month, FormatId $formatId, AbilityId $abilityId) : array;

	/**
	 * Get the ratings for which usage data is available for this month, format, and item.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param ItemId $itemId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndItem(DateTime $month, FormatId $formatId, ItemId $itemId) : array;

	/**
	 * Get the ratings for which usage data is available for this month, format, and move.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param MoveId $moveId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndMove(DateTime $month, FormatId $formatId, MoveId $moveId) : array;
}
