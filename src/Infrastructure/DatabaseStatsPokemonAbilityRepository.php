<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Abilities\StatsPokemonAbility;
use Jp\Dex\Domain\Abilities\StatsPokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

final readonly class DatabaseStatsPokemonAbilityRepository implements StatsPokemonAbilityRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon abilities by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonAbility[] Ordered by percent descending.
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
				`a`.`identifier`,
				`an`.`name`,
				`mra`.`percent`,
				`mrap`.`percent` AS `prev_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_abilities` AS `mra`
				ON `urp`.`id` = `mra`.`usage_rated_pokemon_id`
			INNER JOIN `abilities` AS `a`
				ON `mra`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `mra`.`ability_id` = `an`.`ability_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_abilities` AS `mrap`
				ON `urpp`.`id` = `mrap`.`usage_rated_pokemon_id`
				AND `mra`.`ability_id` = `mrap`.`ability_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `an`.`language_id` = :language_id
			ORDER BY `mra`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ability = new StatsPokemonAbility(
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent'],
			);

			$abilities[] = $ability;
		}

		return $abilities;
	}
}
