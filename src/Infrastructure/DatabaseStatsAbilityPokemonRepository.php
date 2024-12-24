<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Usage\StatsAbilityPokemon;
use Jp\Dex\Domain\Usage\StatsAbilityPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseStatsAbilityPokemonRepository implements StatsAbilityPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats ability Pokémon by month, format, rating, and ability.
	 *
	 * @return StatsAbilityPokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`urp`.`usage_percent` AS `pokemon_percent`,
				`mra`.`percent` AS `ability_percent`,
				`urp`.`usage_percent` * `mra`.`percent` / 100 AS `usage_percent`,
				`urpp`.`usage_percent` * `mrap`.`percent` / 100 AS `prev_percent`,
				`vp`.`base_spe`
			FROM `usage_rated_pokemon` as `urp`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `urp`.`pokemon_id` = `vp`.`pokemon_id`
			INNER JOIN `moveset_rated_abilities` as `mra`
				ON `urp`.`id` = `mra`.`usage_rated_pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `usage_rated_pokemon` as `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_abilities` as `mrap`
				ON `urpp`.`id` = `mrap`.`usage_rated_pokemon_id`
				AND `mra`.`ability_id` = `mrap`.`ability_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `mra`.`ability_id` = :ability_id
				AND `pn`.`language_id` = :language_id
			ORDER BY `usage_percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth?->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsAbilityPokemon(
				$result['icon'] ?? '',
				$result['identifier'],
				$result['name'],
				(float) $result['pokemon_percent'],
				(float) $result['ability_percent'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent'],
				$result['base_spe'],
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}
}
