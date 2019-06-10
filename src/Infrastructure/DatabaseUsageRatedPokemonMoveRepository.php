<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonMove;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonMoveRepositoryInterface;
use PDO;

class DatabaseUsageRatedPokemonMoveRepository implements UsageRatedPokemonMoveRepositoryInterface
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
	 * Get usage rated Pokémon move records by their month, format, rating, and
	 * move. Indexed by Pokémon id value.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param MoveId $moveId
	 *
	 * @return UsageRatedPokemonMove[]
	 */
	public function getByMonthAndFormatAndRatingAndMove(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`pokemon_id`,
				`u`.`usage_percent` AS `pokemon_percent`,
				`m`.`percent` AS `move_percent`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_moves` AS `m`
				ON `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`month` = :month
				AND `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `m`.`move_id` = :move_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonMove = new UsageRatedPokemonMove(
				$month,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				(float) $result['pokemon_percent'],
				$moveId,
				(float) $result['move_percent'],
				(float) $result['usage_percent']
			);

			$usageRatedPokemonMoves[$result['pokemon_id']] = $usageRatedPokemonMove;
		}

		return $usageRatedPokemonMoves;
	}

	/**
	 * Get usage rated Pokémon move records by their format, rating, Pokémon,
	 * and move. Use this to create a trend line for the usage of a specific
	 * Pokémon with a specific move. Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 *
	 * @return UsageRatedPokemonMove[]
	 */
	public function getByFormatAndRatingAndPokemonAndMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`month`,
				`u`.`usage_percent` AS `pokemon_percent`,
				`m`.`percent` AS `move_percent`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_moves` AS `m`
				ON `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `u`.`pokemon_id` = :pokemon_id
				AND `m`.`move_id` = :move_id
			ORDER BY `u`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonMove = new UsageRatedPokemonMove(
				new DateTime($result['month']),
				$formatId,
				$rating,
				$pokemonId,
				(float) $result['pokemon_percent'],
				$moveId,
				(float) $result['move_percent'],
				(float) $result['usage_percent']
			);

			$usageRatedPokemonMoves[$result['month']] = $usageRatedPokemonMove;
		}

		return $usageRatedPokemonMoves;
	}
}
