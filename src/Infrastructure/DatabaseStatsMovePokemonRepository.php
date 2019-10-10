<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Usage\StatsMovePokemon;
use Jp\Dex\Domain\Usage\StatsMovePokemonRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseStatsMovePokemonRepository implements StatsMovePokemonRepositoryInterface
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
	 * Get stats move Pokémon by month, format, rating, and move.
	 *
	 * @param DateTime $month
	 * @param DateTime|null $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param MoveId $moveId
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return StatsMovePokemon[] Ordered by usage percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		MoveId $moveId,
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
				`mrm`.`percent` AS `move_percent`,
				`urp`.`usage_percent` * `mrm`.`percent` / 100 AS `usage_percent`,
				`urpp`.`usage_percent` * `mrmp`.`percent` / 100 AS `prev_percent`
			FROM `moveset_rated_moves` AS `mrm`
			INNER JOIN `usage_rated_pokemon` AS `urp`
				ON `mrm`.`month` = `urp`.`month`
				AND `mrm`.`format_id` = `urp`.`format_id`
				AND `mrm`.`rating` = `urp`.`rating`
				AND `mrm`.`pokemon_id` = `urp`.`pokemon_id`
			INNER JOIN `form_icons` AS `fi`
				ON `mrm`.`pokemon_id` = `fi`.`form_id`
			INNER JOIN `pokemon` AS `p`
				ON `mrm`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `mrm`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `moveset_rated_moves` AS `mrmp`
				ON `mrmp`.`month` = :prev_month
				AND `mrm`.`format_id` = `mrmp`.`format_id`
				AND `mrm`.`rating` = `mrmp`.`rating`
				AND `mrm`.`pokemon_id` = `mrmp`.`pokemon_id`
				AND `mrm`.`move_id` = `mrmp`.`move_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `mrmp`.`month` = `urpp`.`month`
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			WHERE `mrm`.`month` = :month
				AND `mrm`.`format_id` = :format_id
				AND `mrm`.`rating` = :rating
				AND `mrm`.`move_id` = :move_id
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
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsMovePokemon(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				(float) $result['pokemon_percent'],
				(float) $result['move_percent'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent']
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}
}
