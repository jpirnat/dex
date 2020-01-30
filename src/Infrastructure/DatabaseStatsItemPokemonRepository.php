<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Usage\StatsItemPokemon;
use Jp\Dex\Domain\Usage\StatsItemPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseStatsItemPokemonRepository implements StatsItemPokemonRepositoryInterface
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
	 * Get stats item Pokémon by month, format, rating, and item.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param ItemId $itemId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsItemPokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		ItemId $itemId,
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`urp`.`usage_percent` AS `pokemon_percent`,
				`mri`.`percent` AS `item_percent`,
				`urp`.`usage_percent` * `mri`.`percent` / 100 AS `usage_percent`,
				`urpp`.`usage_percent` * `mrip`.`percent` / 100 AS `prev_percent`
			FROM `moveset_rated_items` AS `mri`
			INNER JOIN `usage_rated_pokemon` AS `urp`
				ON `mri`.`month` = `urp`.`month`
				AND `mri`.`format_id` = `urp`.`format_id`
				AND `mri`.`rating` = `urp`.`rating`
				AND `mri`.`pokemon_id` = `urp`.`pokemon_id`
			INNER JOIN `form_icons` AS `fi`
				ON `mri`.`pokemon_id` = `fi`.`form_id`
			INNER JOIN `pokemon` AS `p`
				ON `mri`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `mri`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `moveset_rated_items` AS `mrip`
				ON `mrip`.`month` = :prev_month
				AND `mri`.`format_id` = `mrip`.`format_id`
				AND `mri`.`rating` = `mrip`.`rating`
				AND `mri`.`pokemon_id` = `mrip`.`pokemon_id`
				AND `mri`.`item_id` = `mrip`.`item_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `mrip`.`month` = `urpp`.`month`
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			WHERE `mri`.`month` = :month
				AND `mri`.`format_id` = :format_id
				AND `mri`.`rating` = :rating
				AND `mri`.`item_id` = :item_id
				AND `fi`.`generation_id` = :generation_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			ORDER BY `usage_percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':prev_month', $prevMonth, PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsItemPokemon(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				(float) $result['pokemon_percent'],
				(float) $result['item_percent'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent']
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}
}
