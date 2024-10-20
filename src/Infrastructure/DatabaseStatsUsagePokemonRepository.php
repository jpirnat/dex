<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Usage\StatsUsagePokemon;
use Jp\Dex\Domain\Usage\StatsUsagePokemonRepositoryInterface;
use PDO;

final readonly class DatabaseStatsUsagePokemonRepository implements StatsUsagePokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stats usage Pokémon by month, format, and rating.
	 *
	 * @return StatsUsagePokemon[] Ordered by rank ascending.
	 */
	public function getByMonth(
		DateTime $month,
		?DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		LanguageId $languageId,
	) : array {
		$prevMonth = $prevMonth !== null
			? $prevMonth->format('Y-m-01')
			: null;

		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`rank`,
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`urp`.`usage_percent`,
				`urpp`.`usage_percent` AS `prev_percent`,
				`up`.`raw`,
				`up`.`raw_percent`,
				`up`.`real`,
				`up`.`real_percent`,
				`vp`.`base_spe`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `urp`.`pokemon_id` = `vp`.`pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			LEFT JOIN `usage_rated_pokemon` AS `urpp`
				ON `urpp`.`month` = :prev_month
				AND `urp`.`format_id` = `urpp`.`format_id`
				AND `urp`.`rating` = `urpp`.`rating`
				AND `urp`.`pokemon_id` = `urpp`.`pokemon_id`
			INNER JOIN `usage_pokemon` AS `up`
				ON `urp`.`month` = `up`.`month`
				AND `urp`.`format_id` = `up`.`format_id`
				AND `urp`.`pokemon_id` = `up`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `pn`.`language_id` = :language_id
			ORDER BY `urp`.`rank`'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':prev_month', $prevMonth);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new StatsUsagePokemon(
				$result['rank'],
				$result['icon'] ?? '',
				$result['identifier'],
				$result['name'],
				(float) $result['usage_percent'],
				(float) $result['usage_percent'] - (float) $result['prev_percent'],
				$result['raw'],
				(float) $result['raw_percent'],
				$result['real'],
				(float) $result['real_percent'],
				$result['base_spe'],
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}

	/**
	 * Get a stats usage Pokémon by month, format, rating, and Pokémon id.
	 */
	public function getByPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : ?array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`rank`,
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`urp`.`usage_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `urp`.`pokemon_id` = `vp`.`pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `pn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		return [
			'rank' => $result['rank'],
			'icon' => $result['icon'] ?? '',
			'identifier' => $result['identifier'],
			'name' => $result['name'],
			'usagePercent' => (float) $result['usage_percent'],
		];
	}

	/**
	 * Get a stats usage Pokémon by month, format, rating, and rank.
	 */
	public function getByRank(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		int $rank,
		LanguageId $languageId,
	) : ?array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`rank`,
				`vp`.`icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`urp`.`usage_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `formats` AS `f`
				ON `urp`.`format_id` = `f`.`id`
			INNER JOIN `vg_pokemon` AS `vp`
				ON `f`.`version_group_id` = `vp`.`version_group_id`
				AND `urp`.`pokemon_id` = `vp`.`pokemon_id`
			INNER JOIN `pokemon` AS `p`
				ON `urp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `urp`.`pokemon_id` = `pn`.`pokemon_id`
			WHERE `urp`.`month` = :month
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`rank` = :rank
				AND `pn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':rank', $rank, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		return [
			'rank' => $result['rank'],
			'icon' => $result['icon'] ?? '',
			'identifier' => $result['identifier'],
			'name' => $result['name'],
			'usagePercent' => (float) $result['usage_percent'],
		];
	}
}
