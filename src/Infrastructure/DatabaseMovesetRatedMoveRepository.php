<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use PDO;

final class DatabaseMovesetRatedMoveRepository implements MovesetRatedMoveRepositoryInterface
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
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`move_id`,
				`percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:move_id,
				:percent
			)'
		);
		$stmt->bindValue(':month', $movesetRatedMove->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedMove->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedMove->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedMove->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $movesetRatedMove->getMoveId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedMove->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get moveset rated move records by their format, rating, Pokémon, and move.
	 * Use this to create a trend line for a Pokémon's move usage in a format.
	 * Indexed and sorted by month.
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
				`month`,
				`percent`
			FROM `moveset_rated_moves`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
				AND `move_id` = :move_id
			ORDER BY `month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedMove = new MovesetRatedMove(
				new DateTime($result['month']),
				$formatId,
				$rating,
				$pokemonId,
				$moveId,
				(float) $result['percent']
			);

			$movesetRatedMoves[$result['month']] = $movesetRatedMove;
		}

		return $movesetRatedMoves;
	}
}
