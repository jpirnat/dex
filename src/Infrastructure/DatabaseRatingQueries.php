<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\RatingQueriesInterface;
use PDO;

class DatabaseRatingQueries implements RatingQueriesInterface
{
	/** @var PDO $db */
	private $db;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Get the ratings for which usage data is available for this month and format.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormat(DateTime $month, FormatId $formatId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `usage_rated`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the ratings for which usage data is available for this month, format, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndPokemon(DateTime $month, FormatId $formatId, PokemonId $pokemonId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `moveset_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the ratings for which usage data is available for this month, format, and ability.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param AbilityId $abilityId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndAbility(DateTime $month, FormatId $formatId, AbilityId $abilityId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `moveset_rated_abilities`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `ability_id` = :ability_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the ratings for which usage data is available for this month, format, and item.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param ItemId $itemId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndItem(DateTime $month, FormatId $formatId, ItemId $itemId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `moveset_rated_items`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `item_id` = :item_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Get the ratings for which usage data is available for this month, format, and move.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param MoveId $moveId
	 *
	 * @return int[]
	 */
	public function getByMonthAndFormatAndMove(DateTime $month, FormatId $formatId, MoveId $moveId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT DISTINCT
				`rating`
			FROM `moveset_rated_moves`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `move_id` = :move_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
}
