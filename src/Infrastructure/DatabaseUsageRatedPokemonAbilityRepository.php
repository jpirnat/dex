<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonAbility;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonAbilityRepositoryInterface;
use PDO;

class DatabaseUsageRatedPokemonAbilityRepository implements UsageRatedPokemonAbilityRepositoryInterface
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
	 * Get usage rated Pokémon ability records by their year, month, format,
	 * rating, and ability. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param AbilityId $abilityId
	 *
	 * @return UsageRatedPokemonAbility[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndAbility(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`pokemon_id`,
				`u`.`usage_percent` AS `pokemon_percent`,
				`m`.`percent` AS `ability_percent`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_abilities` AS `m`
				ON `u`.`year` = `m`.`year`
				AND `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`year` = :year
				AND `u`.`month` = :month
				AND `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `m`.`ability_id` = :ability_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonAbilities[$result['pokemon_id']] = new UsageRatedPokemonAbility(
				$year,
				$month,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				(float) $result['pokemon_percent'],
				$abilityId,
				(float) $result['ability_percent'],
				(float) $result['usage_percent']
			);
		}

		return $usageRatedPokemonAbilities;
	}
}
