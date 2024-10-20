<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Usage\StatsItemPokemon;
use Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseStatsItemPokemonRepository implements StatsItemPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats item Pokémon by month, format, rating, and item.
	 *
	 * @return StatsItemPokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		ItemId $itemId,
		LanguageId $languageId,
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`urp`.`usage_percent` AS `pokemon_percent`,
				`mri`.`percent` AS `item_percent`,
				`urp`.`usage_percent` * `mri`.`percent` / 100 AS `usage_percent`,
				`urpp`.`usage_percent` * `mrip`.`percent` / 100 AS `prev_percent`,
				`vp`.`base_spe`
			FROM `usage_rated_pokemon` as `urp`
			INNER JOIN `moveset_rated_items` as `mri`
				ON `urp`.`id` = `mri`.`usage_rated_pokemon_id`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `urp`.`pokemon_id` = `vp`.`pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `usage_rated_pokemon` as `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_items` as `mrip`
				ON `urpp`.`id` = `mrip`.`usage_rated_pokemon_id`
				AND `mri`.`item_id` = `mrip`.`item_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `mri`.`item_id` = :item_id
				AND `pn`.`language_id` = :language_id
			ORDER BY `usage_percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsItemPokemon(
				$result['icon'] ?? '',
				$result['identifier'],
				$result['name'],
				(float) $result['pokemon_percent'],
				(float) $result['item_percent'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent'],
				$result['base_spe'],
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}
}
