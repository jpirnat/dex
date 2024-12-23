<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemon;
use Jp\Dex\Domain\Stats\Moveset\MovesetPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseMovesetPokemonRepository implements MovesetPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Do any moveset Pokémon records exist for this month and format?
	 */
	public function hasAny(DateTime $month, FormatId $formatId) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				1
			FROM `moveset_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
			LIMIT 1'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->fetchColumn();
	}

	/**
	 * Save a moveset Pokémon record.
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
		$stmt->bindValue(':month', $movesetPokemon->month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $movesetPokemon->formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetPokemon->pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':raw_count', $movesetPokemon->rawCount, PDO::PARAM_INT);
		$stmt->bindValue(':viability_ceiling', $movesetPokemon->viabilityCeiling, PDO::PARAM_INT);
		$stmt->execute();
	}

	/**
	 * Get a moveset Pokémon record by month, format, and Pokémon.
	 */
	public function getByMonthAndFormatAndPokemon(
		DateTime $month,
		FormatId $formatId,
		PokemonId $pokemonId,
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
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		return new MovesetPokemon(
			$month,
			$formatId,
			$pokemonId,
			$result['raw_count'],
			$result['viability_ceiling'],
		);
	}
}
