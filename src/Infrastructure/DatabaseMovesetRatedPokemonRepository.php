<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;
use PDO;

final class DatabaseMovesetRatedPokemonRepository implements MovesetRatedPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Does a moveset rated Pokémon record exist for this month, format, rating,
	 * and Pokémon?
	 */
	public function has(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_pokemon` AS `mrp`
				ON `urp`.`id` = `mrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Do any moveset rated Pokémon records exist for this month, format, and
	 * rating?
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_pokemon` AS `mrp`
				ON `urp`.`id` = `mrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Count the moveset rated Pokémon records for this start month, end month,
	 * format, rating, and Pokémon.
	 */
	public function count(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : int {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_pokemon` AS `mrp`
				ON `urp`.`id` = `mrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` BETWEEN :start AND :end
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':start', $start->format('Y-m-01'));
		$stmt->bindValue(':end', $end->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchColumn();
	}

	/**
	 * Count the moveset rated Pokémon records for this start month, end month,
	 * format, and rating.
	 *
	 * @return int[] Indexed by Pokémon id.
	 */
	public function countAll(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`pokemon_id`,
				COUNT(*)
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_pokemon` AS `mrp`
				ON `urp`.`id` = `mrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` BETWEEN :start AND :end
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
			GROUP BY `pokemon_id`'
		);
		$stmt->bindValue(':start', $start->format('Y-m-01'));
		$stmt->bindValue(':end', $end->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Save a moveset rated Pokémon record.
	 */
	public function save(MovesetRatedPokemon $movesetRatedPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_pokemon` (
				`usage_rated_pokemon_id`,
				`average_weight`
			) VALUES (
				:urp_id,
				:average_weight
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedPokemon->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':average_weight', $movesetRatedPokemon->getAverageWeight());
		$stmt->execute();
	}

	/**
	 * Get a moveset rated Pokémon record by month, format, rating, and Pokémon.
	 */
	public function getByMonthAndFormatAndRatingAndPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : ?MovesetRatedPokemon {
		$stmt = $this->db->prepare(
			'SELECT
       			`mrp`.`usage_rated_pokemon_id`,
				`mrp`.`average_weight`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_pokemon` AS `mrp`
				ON `urp`.`id` = `mrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		$movesetRatedPokemon = new MovesetRatedPokemon(
			new UsageRatedPokemonId($result['usage_rated_pokemon_id']),
			(float) $result['average_weight']
		);

		return $movesetRatedPokemon;
	}
}
