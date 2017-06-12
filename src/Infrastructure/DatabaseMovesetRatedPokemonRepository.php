<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonNotFoundException;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use PDO;

class DatabaseMovesetRatedPokemonRepository implements MovesetRatedPokemonRepositoryInterface
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
	 * Do any moveset rated Pokémon records exist for this year, month, format,
	 * and rating?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `moveset_rated_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a moveset rated Pokémon record.
	 *
	 * @param MovesetRatedPokemon $movesetRatedPokemon
	 *
	 * @return void
	 */
	public function save(MovesetRatedPokemon $movesetRatedPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`average_weight`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:average_weight
			)'
		);
		$stmt->bindValue(':year', $movesetRatedPokemon->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedPokemon->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedPokemon->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':average_weight', $movesetRatedPokemon->getAverageWeight(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get a moveset rated Pokémon record by year, month, format, rating, and
	 * Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @throws MovesetRatedPokemonNotFoundException if no moveset rated Pokémon
	 *     record exists with this year, month, format, rating, and Pokémon.
	 *
	 * @return MovesetRatedPokemon
	 */
	public function getByYearAndMonthAndFormatAndRatingAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : MovesetRatedPokemon {
		$stmt = $this->db->prepare(
			'SELECT
				`average_weight`
			FROM `moveset_rated_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MovesetRatedPokemonNotFoundException(
				'No moveset rated Pokémon record exists with year ' . $year
				. ', month ' . $month . ', format id ' . $formatId->value()
				. ', rating ' . $rating . ', and Pokémon id '
				. $pokemonId->value()
			);
		}

		$movesetRatedPokemon = new MovesetRatedPokemon(
			$year,
			$month,
			$formatId,
			$rating,
			$pokemonId,
			(float) $result['average_weight']
		);

		return $movesetRatedPokemon;
	}
}
