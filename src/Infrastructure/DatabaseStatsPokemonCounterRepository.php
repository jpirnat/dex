<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Counters\StatsPokemonCounter;
use Jp\Dex\Domain\Counters\StatsPokemonCounterRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

final readonly class DatabaseStatsPokemonCounterRepository implements StatsPokemonCounterRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon counters by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonCounter[] Ordered by score descending.
	 */
	public function getByMonth(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`mrc`.`number1` AS `score`,
				`mrc`.`number2` AS `percent`,
				`mrc`.`number3` AS `standard_deviation`,
				`mrc`.`percent_knocked_out`,
				`mrc`.`percent_switched_out`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_counters` AS `mrc`
				ON `urp`.`id` = `mrc`.`usage_rated_pokemon_id`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `mrc`.`counter_id` = `vp`.`pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `mrc`.`counter_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `mrc`.`counter_id` = `pn`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `pn`.`language_id` = :language_id
			ORDER BY `mrc`.`number1` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$counters = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$counter = new StatsPokemonCounter(
				$result['icon'] ?? '',
				$result['identifier'],
				$result['name'],
				(float) $result['score'],
				(float) $result['percent'],
				(float) $result['standard_deviation'],
				(float) $result['percent_knocked_out'],
				(float) $result['percent_switched_out'],
			);

			$counters[] = $counter;
		}

		return $counters;
	}
}
