<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use PDO;

class DatabaseMovesetRatedMoveRepository implements MovesetRatedMoveRepositoryInterface
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
	 * Save a moveset rated move record.
	 *
	 * @param MovesetRatedMove $movesetRatedMove
	 *
	 * @return void
	 */
	public function save(MovesetRatedMove $movesetRatedMove) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_moves` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`move_id`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:move_id,
				:percent
			)'
		);
		$stmt->bindValue(':year', $movesetRatedMove->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedMove->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedMove->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedMove->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedMove->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $movesetRatedMove->getMoveId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedMove->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get moveset rated move records by year, month, format, rating, and
	 * Pokémon. Indexed by move id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedMove[]
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
				`move_id`,
				`percent`
			FROM `moveset_rated_moves`
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

		$movesetRatedMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedMoves[$result['move_id']] = new MovesetRatedMove(
				$year,
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new MoveId($result['move_id']),
				(float) $result['percent']
			);
		}

		return $movesetRatedMoves;
	}

	/**
	 * Get moveset rated move records by their format, rating, Pokémon, and move.
	 * Use this to create a trend line for a Pokémon's move usage in a format.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param MoveId $moveId
	 *
	 * @return MovesetRatedMove[]
	 */
	public function getByFormatAndRatingAndPokemonAndMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`,
				`percent`
			FROM `moveset_rated_moves`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
				AND `move_id` = :move_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedMoves[] = new MovesetRatedMove(
				$result['year'],
				$result['month'],
				$formatId,
				$rating,
				$pokemonId,
				$moveId,
				(float) $result['percent']
			);
		}

		return $movesetRatedMoves;
	}
}
