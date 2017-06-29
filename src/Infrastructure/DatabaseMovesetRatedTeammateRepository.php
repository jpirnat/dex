<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammate;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;
use PDO;
use PDOException;

class DatabaseMovesetRatedTeammateRepository implements MovesetRatedTeammateRepositoryInterface
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
	 * Save a moveset rated teammate record.
	 *
	 * @param MovesetRatedTeammate $movesetRatedTeammate
	 *
	 * @return void
	 */
	public function save(MovesetRatedTeammate $movesetRatedTeammate) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_teammates` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`teammate_id`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:teammate_id,
				:percent
			)'
		);
		$stmt->bindValue(':year', $movesetRatedTeammate->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedTeammate->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedTeammate->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedTeammate->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedTeammate->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':teammate_id', $movesetRatedTeammate->getTeammateId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedTeammate->getPercent(), PDO::PARAM_STR);
		try {
			$stmt->execute();
		} catch (PDOException $e) {
			// This record already exists.
			// Bug fix for http://www.smogon.com/stats/2014-11/moveset/anythinggoes-0.txt
			// in which Inkay has teammate Abra twice.
		}
	}

	/**
	 * Get moveset rated teammate records by year, month, format, rating, and
	 * Pokémon. Indexed by teammate id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedTeammate[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`teammate_id`,
				`percent`
			FROM `moveset_rated_teammates`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedTeammates = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedTeammates[$result['teammate_id']] = new MovesetRatedTeammate(
				$year,
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new PokemonId($result['teammate_id']),
				(float) $result['percent']
			);
		}

		return $movesetRatedTeammates;
	}
}
