<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use PDO;

final class DatabaseMovesetPokemonRepository implements MovesetPokemonRepositoryInterface
{
	private PDO $db;

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
	 * Do any moveset Pokémon records exist for this month and format?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `moveset_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
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
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw_count`,
				`viability_ceiling`
			) VALUES (
				:month,
				:format_id,
				:pokemon_id,
				:raw_count,
				:viability_ceiling
			)'
		);
		$stmt->bindValue(':month', $movesetPokemon->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_count', $movesetPokemon->getRawCount(), PDO::PARAM_INT);
		$stmt->bindValue(':viability_ceiling', $movesetPokemon->getViabilityCeiling(), PDO::PARAM_INT);
		$stmt->execute();
	}

	/**
	 * Get a moveset Pokémon record by month, format, and Pokémon.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetPokemon|null
	 */
	public function getByMonthAndFormatAndPokemon(
		DateTime $month,
		FormatId $formatId,
		PokemonId $pokemonId
	) : ?MovesetPokemon {
		$stmt = $this->db->prepare(
			'SELECT
				`raw_count`,
				`viability_ceiling`
			FROM `moveset_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		$movesetPokemon = new MovesetPokemon(
			$month,
			$formatId,
			$pokemonId,
			$result['raw_count'],
			$result['viability_ceiling']
		);

		return $movesetPokemon;
	}
}
