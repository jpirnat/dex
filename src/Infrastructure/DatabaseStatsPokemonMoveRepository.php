<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\StatsPokemonMove;
use Jp\Dex\Domain\Moves\StatsPokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseStatsPokemonMoveRepository implements StatsPokemonMoveRepositoryInterface
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
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`mrm`.`percent`,
				`mrmp`.`percent` AS `prev_percent`,

				`t`.`identifier` AS `type_identifier`,
				`tn`.`name` AS `type_name`,
				`ti`.`icon` AS `type_icon`,
				`c`.`icon` AS `category_icon`,
				`cn`.`name` AS `category_name`,
				`vgm`.`pp`,
				`vgm`.`power`,
				`vgm`.`accuracy`,
				`vgm`.`priority`,
				`vgm`.`target_id`

			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_moves` AS `mrm`
				ON `urp`.`id` = `mrm`.`usage_rated_pokemon_id`
			INNER JOIN `moves` AS `m`
				ON `mrm`.`move_id` = `m`.`id`
			INNER JOIN `move_names` AS `mn`
				ON `mrm`.`move_id` = `mn`.`move_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			LEFT JOIN `moveset_rated_moves` AS `mrmp`
				ON `urpp`.`id` = `mrmp`.`usage_rated_pokemon_id`
				AND `mrm`.`move_id` = `mrmp`.`move_id`

			INNER JOIN `vg_moves` AS `vgm`
				ON `vgm`.`version_group_id` = :version_group_id
				AND `mrm`.`move_id` = `vgm`.`move_id`
			INNER JOIN `types` AS `t`
				ON `vgm`.`type_id` = `t`.`id`
			INNER JOIN `type_names` AS `tn`
				ON `mn`.`language_id` = `tn`.`language_id`
				AND `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `mn`.`language_id` = `ti`.`language_id`
				AND `t`.`id` = `ti`.`type_id`
			INNER JOIN `categories` AS `c`
				ON `vgm`.`category_id` = `c`.`id`
			INNER JOIN `category_names` AS `cn`
				ON `mn`.`language_id` = `cn`.`language_id`
				AND `c`.`id` = `cn`.`category_id`

			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `mn`.`language_id` = :language_id
			ORDER BY `mrm`.`percent` DESC'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$moves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$move = new StatsPokemonMove(
				$result['identifier'],
				$result['name'],
				(float) $result['percent'],
				(float) $result['percent'] - (float) $result['prev_percent'],
				new DexType(
					$result['type_identifier'],
					$result['type_name'],
					$result['type_icon'] ?? '',
				),
				new DexCategory(
					$result['category_icon'],
					$result['category_name'],
				),
				$result['pp'],
				$result['power'],
				$result['accuracy'],
				$result['priority'],
				new TargetId($result['target_id']),
			);

			$moves[] = $move;
		}

		return $moves;
	}
}
