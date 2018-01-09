<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonMove;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonMoveRepositoryInterface;
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
	 * Get usage rated Pokémon move records by their year, month, format,
	 * rating, and move. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param MoveId $moveId
	 *
	 * @return UsageRatedPokemonMove[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndMove(
		int $year,
		int $month,
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
				ON `u`.`year` = `m`.`year`
				AND `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`year` = :year
				AND `u`.`month` = :month
				AND `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `m`.`move_id` = :move_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonMoves[$result['pokemon_id']] = new UsageRatedPokemonMove(
				$year,
				$month,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				(float) $result['pokemon_percent'],
				$moveId,
				(float) $result['move_percent'],
				(float) $result['usage_percent']
			);
		}

		return $usageRatedPokemonMoves;
	}
}
