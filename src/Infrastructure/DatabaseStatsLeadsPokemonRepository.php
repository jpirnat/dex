<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Leads\StatsLeadsPokemon;
use Jp\Dex\Domain\Leads\StatsLeadsPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseStatsLeadsPokemonRepository implements StatsLeadsPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats leads Pokémon by month, format, and rating.
	 *
	 * @return StatsLeadsPokemon[] Ordered by rank ascending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`lrp`.`rank`,
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`lrp`.`usage_percent`,
				`lrpp`.`usage_percent` AS `prev_percent`,
				`lp`.`raw`,
				`lp`.`raw_percent`,
				`vp`.`base_spe`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `leads_rated_pokemon` AS `lrp`
				ON `urp`.`id` = `lrp`.`usage_rated_pokemon_id`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `urp`.`pokemon_id` = `vp`.`pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `leads_rated_pokemon` AS `lrpp`
				ON `urpp`.`id` = `lrpp`.`usage_rated_pokemon_id`
			INNER JOIN `leads_pokemon` AS `lp`
				ON `urp`.`month` = `lp`.`month`
				AND `urp`.`format_id` = `lp`.`format_id`
				AND `urp`.`pokemon_id` = `lp`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `pn`.`language_id` = :language_id
			ORDER BY `lrp`.`rank`'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth?->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsLeadsPokemon(
				$result['rank'],
				$result['icon'] ?? '',
				$result['identifier'],
				$result['name'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent'],
				$result['raw'],
				(float) $result['raw_percent'],
				$result['base_spe'],
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}
}
