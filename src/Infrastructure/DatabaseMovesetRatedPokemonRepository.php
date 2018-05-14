<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
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
	 * Does a moveset rated Pokémon record exist for this month, format, rating,
	 * and Pokémon?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return bool
	 */
	public function has(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `moveset_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Do any moveset rated Pokémon records exist for this month, format, and
	 * rating?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `moveset_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
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
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`average_weight`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:average_weight
			)'
		);
		$stmt->bindValue(':month', $movesetRatedPokemon->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedPokemon->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':average_weight', $movesetRatedPokemon->getAverageWeight(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get a moveset rated Pokémon record by month, format, rating, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @throws MovesetRatedPokemonNotFoundException if no moveset rated Pokémon
	 *     record exists with this month, format, rating, and Pokémon.
	 *
	 * @return MovesetRatedPokemon
	 */
	public function getByMonthAndFormatAndRatingAndPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : MovesetRatedPokemon {
		$stmt = $this->db->prepare(
			'SELECT
				`average_weight`
			FROM `moveset_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MovesetRatedPokemonNotFoundException(
				'No moveset rated Pokémon record exists with month '
				. $month->format('Y-m') . ', format id '
				. $formatId->value() . ', rating ' . $rating
				. ', and Pokémon id ' . $pokemonId->value()
			);
		}

		$movesetRatedPokemon = new MovesetRatedPokemon(
			$month,
			$formatId,
			$rating,
			$pokemonId,
			(float) $result['average_weight']
		);

		return $movesetRatedPokemon;
	}
}
