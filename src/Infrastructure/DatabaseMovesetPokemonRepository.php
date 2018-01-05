<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonNotFoundException;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use PDO;

class DatabaseMovesetPokemonRepository implements MovesetPokemonRepositoryInterface
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
	 * Do any moveset Pokémon records exist for this year, month, and format?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function hasAny(
		int $year,
		int $month,
		FormatId $formatId
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `moveset_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a moveset Pokémon record.
	 *
	 * @param MovesetPokemon $movesetPokemon
	 *
	 * @return void
	 */
	public function save(MovesetPokemon $movesetPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw_count`,
				`viability_ceiling`
			) VALUES (
				:year,
				:month,
				:format_id,
				:pokemon_id,
				:raw_count,
				:viability_ceiling
			)'
		);
		$stmt->bindValue(':year', $movesetPokemon->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetPokemon->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_count', $movesetPokemon->getRawCount(), PDO::PARAM_INT);
		$stmt->bindValue(':viability_ceiling', $movesetPokemon->getViabilityCeiling(), PDO::PARAM_INT);
		$stmt->execute();
	}

	/**
	 * Get a moveset Pokémon record by year, month, format, and Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @throws MovesetPokemonNotFoundException if no moveset Pokémon record
	 *     exists with this year, month, format, and Pokémon.
	 *
	 * @return MovesetPokemon
	 */
	public function getByYearAndMonthAndFormatAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		PokemonId $pokemonId
	) : MovesetPokemon {
		$stmt = $this->db->prepare(
			'SELECT
				`raw_count`,
				`viability_ceiling`
			FROM `moveset_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MovesetPokemonNotFoundException(
				'No moveset Pokémon record exists with year ' . $year
				. ', month ' . $month . ', format id ' . $formatId->value()
				. ', and Pokémon id ' . $pokemonId->value()
			);
		}

		$movesetPokemon = new MovesetPokemon(
			$year,
			$month,
			$formatId,
			$pokemonId,
			$result['raw_count'],
			$result['viability_ceiling']
		);

		return $movesetPokemon;
	}
}
