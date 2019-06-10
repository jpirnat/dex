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

class DatabaseStatsLeadsPokemonRepository implements StatsLeadsPokemonRepositoryInterface
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
			FROM `leads_rated_pokemon` AS `lrp`
			INNER JOIN `form_icons` AS `fi`
				ON `lrp`.`pokemon_id` = `fi`.`form_id`
			INNER JOIN `pokemon` AS `p`
				ON `lrp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `lrp`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `leads_rated_pokemon` AS `lrpp`
				ON `lrpp`.`month` = :prev_month
				AND `lrp`.`format_id` = `lrpp`.`format_id`
				AND `lrp`.`rating` = `lrpp`.`rating`
				AND `lrp`.`pokemon_id` = `lrpp`.`pokemon_id`
			INNER JOIN `leads_pokemon` AS `lp`
				ON `lrp`.`month` = `lp`.`month`
				AND `lrp`.`format_id` = `lp`.`format_id`
				AND `lrp`.`pokemon_id` = `lp`.`pokemon_id`
			WHERE `lrp`.`month` = :month
				AND `lrp`.`format_id` = :format_id
				AND `lrp`.`rating` = :rating
				AND `fi`.`generation_id` = :generation_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			ORDER BY `lrp`.`rank` ASC'
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
