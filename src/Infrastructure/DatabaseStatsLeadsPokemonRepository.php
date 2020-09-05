<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Leads\StatsLeadsPokemon;
use Jp\Dex\Domain\Leads\StatsLeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseStatsLeadsPokemonRepository implements StatsLeadsPokemonRepositoryInterface
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
	 * Get stats leads Pokémon by month, format, and rating.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsLeadsPokemon[] Ordered by rank ascending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`lrp`.`rank`,
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`lrp`.`usage_percent`,
				`lrpp`.`usage_percent` AS `prev_percent`,
				`lp`.`raw`,
				`lp`.`raw_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `leads_rated_pokemon` AS `lrp`
				ON `urp`.`id` = `lrp`.`usage_rated_pokemon_id`
			INNER JOIN `form_icons` AS `fi`
				ON `urp`.`pokemon_id` = `fi`.`form_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `leads_rated_pokemon` AS `lrpp`
				ON `urpp`.`id` = `lrpp`.`usage_rated_pokemon_id`
			INNER JOIN `leads_pokemon` AS `lp`
				ON `urp`.`month` = `lp`.`month`
				AND `urp`.`format_id` = `lp`.`format_id`
				AND `urp`.`pokemon_id` = `lp`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `fi`.`generation_id` = :generation_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			ORDER BY `lrp`.`rank`'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':prev_month', $prevMonth, PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsLeadsPokemon(
				$result['rank'],
				$result['icon'],
				$result['identifier'],
				$result['name'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent'],
				$result['raw'],
				(float) $result['raw_percent']
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}
}
