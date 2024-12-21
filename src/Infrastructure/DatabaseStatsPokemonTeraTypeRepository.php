<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\StatsPokemonTeraType;
use Jp\Dex\Domain\Types\StatsPokemonTeraTypeRepositoryInterface;
use PDO;

final readonly class DatabaseStatsPokemonTeraTypeRepository implements StatsPokemonTeraTypeRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats Pokémon Tera types by month, format, rating, and Pokémon.
	 *
	 * @return StatsPokemonTeraType[] Ordered by percent descending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`ti`.`icon`,
				`t`.`identifier`,
				`tn`.`name`,
				`mrt`.`percent`,
				`mrtp`.`percent` AS `prev_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_tera_types` AS `mrt`
				ON `urp`.`id` = `mrt`.`usage_rated_pokemon_id`
			INNER JOIN `types` AS `t`
				ON `mrt`.`type_id` = `t`.`id`
			INNER JOIN `type_names` AS `tn`
				ON `mrt`.`type_id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `tn`.`language_id` = `ti`.`language_id`
				AND `tn`.`type_id` = `ti`.`type_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_tera_types` AS `mrtp`
				ON `urpp`.`id` = `mrtp`.`usage_rated_pokemon_id`
				AND `mrt`.`type_id` = `mrtp`.`type_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `tn`.`language_id` = :language_id
			ORDER BY `mrt`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth?->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$teraTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$teraType = new StatsPokemonTeraType(
				$result['icon'] ?? '',
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent'],
			);

			$teraTypes[] = $teraType;
		}

		return $teraTypes;
	}
}
