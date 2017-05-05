<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use PDO;

class DatabaseMovesetRatedAbilityRepository implements MovesetRatedAbilityRepositoryInterface
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
	 * Save a moveset rated ability record.
	 *
	 * @param MovesetRatedAbility $movesetRatedAbility
	 *
	 * @return void
	 */
	public function save(MovesetRatedAbility $movesetRatedAbility) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_abilities` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`ability_id`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:ability_id,
				:percent
			)'
		);
		$stmt->bindValue(':year', $movesetRatedAbility->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedAbility->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedAbility->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedAbility->rating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedAbility->pokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $movesetRatedAbility->abilityId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedAbility->percent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get moveset rated ability records by year, month, format, rating, and
	 * Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAbility[]
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
				`ability_id`,
				`percent`
			FROM `moveset_rated_abilities`
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

		$movesetRatedAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedAbilities[] = new MovesetRatedAbility(
				$year,
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new AbilityId($result['ability_id']),
				(float) $result['percent']
			);
		}

		return $movesetRatedAbilities;
	}

	/**
	 * Get moveset rated ability records by format and rating and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`,
				`ability_id`,
				`percent`
			FROM `moveset_rated_abilities`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedAbilities[] = new MovesetRatedAbility(
				$result['year'],
				$result['month'],
				$formatId,
				$rating,
				$pokemonId,
				new AbilityId($result['ability_id']),
				(float) $result['percent']
			);
		}

		return $movesetRatedAbilities;
	}

	/**
	 * Get moveset rated ability records by format and Pokémon and ability.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getByFormatAndPokemonAndAbility(
		FormatId $formatId,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`,
				`rating`,
				`percent`
			FROM `moveset_rated_abilities`
			WHERE `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id
				AND `ability_id` = :ability_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedAbilities[] = new MovesetRatedAbility(
				$result['year'],
				$result['month'],
				$formatId,
				$result['rating'],
				$pokemonId,
				$abilityId,
				(float) $result['percent']
			);
		}

		return $movesetRatedAbilities;
	}
}
