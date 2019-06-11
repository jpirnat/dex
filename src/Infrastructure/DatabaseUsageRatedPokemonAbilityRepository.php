<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
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
	 * Get usage rated Pokémon ability records by their format, rating, Pokémon,
	 * and ability. Use this to create a trend line for the usage of a specific
	 * Pokémon with a specific ability. Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 *
	 * @return UsageRatedPokemonAbility[]
	 */
	public function getByFormatAndRatingAndPokemonAndAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`month`,
				`u`.`usage_percent` AS `pokemon_percent`,
				`m`.`percent` AS `ability_percent`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_abilities` AS `m`
				ON `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `u`.`pokemon_id` = :pokemon_id
				AND `m`.`ability_id` = :ability_id
			ORDER BY `u`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonAbility = new UsageRatedPokemonAbility(
				new DateTime($result['month']),
				$formatId,
				$rating,
				$pokemonId,
				(float) $result['pokemon_percent'],
				$abilityId,
				(float) $result['ability_percent'],
				(float) $result['usage_percent']
			);

			$usageRatedPokemonAbilities[$result['month']] = $usageRatedPokemonAbility;
		}

		return $usageRatedPokemonAbilities;
	}
}
