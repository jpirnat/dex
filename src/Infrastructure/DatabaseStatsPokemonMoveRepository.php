<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\StatsPokemonMove;
use Jp\Dex\Domain\Moves\StatsPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

final class DatabaseStatsPokemonMoveRepository implements StatsPokemonMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon moves by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonMove[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`i`.`identifier`,
				`in`.`name`,
				`mrm`.`percent`,
				`mrmp`.`percent` AS `prev_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_moves` AS `mrm`
				ON `urp`.`id` = `mrm`.`usage_rated_pokemon_id`
			INNER JOIN `moves` AS `i`
				ON `mrm`.`move_id` = `i`.`id`
			INNER JOIN `move_names` AS `in`
				ON `mrm`.`move_id` = `in`.`move_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_moves` AS `mrmp`
				ON `urpp`.`id` = `mrmp`.`usage_rated_pokemon_id`
				AND `mrm`.`move_id` = `mrmp`.`move_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `in`.`language_id` = :language_id
			ORDER BY `mrm`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$moves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$move = new StatsPokemonMove(
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent'],
			);

			$moves[] = $move;
		}

		return $moves;
	}
}
