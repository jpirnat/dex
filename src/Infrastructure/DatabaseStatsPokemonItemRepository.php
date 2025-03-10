<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\StatsPokemonItem;
use Jp\Dex\Domain\Items\StatsPokemonItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseStatsPokemonItemRepository implements StatsPokemonItemRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon items by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonItem[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`vgi`.`icon`,
				`i`.`identifier`,
				COALESCE(`id`.`name`, `in`.`name`) AS `name`,
				`mri`.`percent`,
				`mrip`.`percent` AS `prev_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_items` AS `mri`
				ON `urp`.`id` = `mri`.`usage_rated_pokemon_id`
			INNER JOIN `items` AS `i`
				ON `mri`.`item_id` = `i`.`id`
			INNER JOIN `item_names` AS `in`
				ON `mri`.`item_id` = `in`.`item_id`
			LEFT JOIN `vg_items` AS `vgi`
				ON `mri`.`item_id` = `vgi`.`item_id`
				AND `vgi`.`version_group_id` = :version_group_id
			LEFT JOIN `item_descriptions` AS `id`
				ON `vgi`.`version_group_id` = `id`.`version_group_id`
				AND `in`.`language_id` = `id`.`language_id`
				AND `i`.`id` = `id`.`item_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_items` AS `mrip`
				ON `urpp`.`id` = `mrip`.`usage_rated_pokemon_id`
				AND `mri`.`item_id` = `mrip`.`item_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `in`.`language_id` = :language_id
			ORDER BY `mri`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth?->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$items = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = new StatsPokemonItem(
				(string) $result['icon'],
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent'],
			);

			$items[] = $item;
		}

		return $items;
	}
}
