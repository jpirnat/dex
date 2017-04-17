<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use PDO;

class DatabaseMovesetPokemonRepository implements MovesetPokemonRepositoryInterface
{
	/** @var PDO $db */
	protected $db;

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
	public function has(
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
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
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
		$stmt->bindValue(':year', $movesetPokemon->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetPokemon->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetPokemon->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetPokemon->pokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_count', $movesetPokemon->rawCount(), PDO::PARAM_INT);
		$stmt->bindValue(':viability_ceiling', $movesetPokemon->viabilityCeiling(), PDO::PARAM_INT);
		$stmt->execute();
	}
}
