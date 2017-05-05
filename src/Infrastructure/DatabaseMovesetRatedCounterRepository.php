<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounter;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use PDO;

class DatabaseMovesetRatedCounterRepository implements MovesetRatedCounterRepositoryInterface
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
	 * Save a moveset rated counter record.
	 *
	 * @param MovesetRatedCounter $movesetRatedCounter
	 *
	 * @return void
	 */
	public function save(MovesetRatedCounter $movesetRatedCounter) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_counters` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`counter_id`,
				`number1`,
				`number2`,
				`number3`,
				`percent_knocked_out`,
				`percent_switched_out`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:counter_id,
				:number1,
				:number2,
				:number3,
				:percent_knocked_out,
				:percent_switched_out
			)'
		);
		$stmt->bindValue(':year', $movesetRatedCounter->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedCounter->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedCounter->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedCounter->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedCounter->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':counter_id', $movesetRatedCounter->getCounterId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':number1', $movesetRatedCounter->getNumber1(), PDO::PARAM_STR);
		$stmt->bindValue(':number2', $movesetRatedCounter->getNumber2(), PDO::PARAM_STR);
		$stmt->bindValue(':number3', $movesetRatedCounter->getNumber3(), PDO::PARAM_STR);
		$stmt->bindValue(':percent_knocked_out', $movesetRatedCounter->getPercentKnockedOut(), PDO::PARAM_STR);
		$stmt->bindValue(':percent_switched_out', $movesetRatedCounter->getPercentSwitchedOut(), PDO::PARAM_STR);
		$stmt->execute();
	}


	/**
	 * Get moveset rated counter records by year, month, format, rating, and
	 * Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedCounter[]
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
				`counter_id`,
				`number1`,
				`number2`,
				`number3`,
				`percent_knocked_out`,
				`percent_switched_out`
			FROM `moveset_rated_counters`
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

		$movesetRatedCounters = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedCounters[] = new MovesetRatedCounter(
				$year,
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new PokemonId($result['counter_id']),
				(float) $result['number1'],
				(float) $result['number2'],
				(float) $result['number3'],
				(float) $result['percent_knocked_out'],
				(float) $result['percent_switched_out']
			);
		}

		return $movesetRatedCounters;
	}
}
