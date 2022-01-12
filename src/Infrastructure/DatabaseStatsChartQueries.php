<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\StatsChartQueriesInterface;
use PDO;

final class DatabaseStatsChartQueries implements StatsChartQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the months that have data recorded for this format and rating.
	 *
	 * @return array Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMonthsWithData(FormatId $formatId, int $rating) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`month`
			FROM `usage_rated`
			WHERE `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$months = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$months[$result['month']] = 1;
		}

		return $months;
	}

	/**
	 * Get usage data for the usage chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsage(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`month`,
				`usage_percent`
			FROM `usage_rated_pokemon`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			ORDER BY `month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['usage_percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the lead usage chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getLeadUsage(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`month`,
				`lrp`.`usage_percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `leads_rated_pokemon` AS `lrp`
				ON `urp`.`id` = `lrp`.`usage_rated_pokemon_id`
			WHERE `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
			ORDER BY `urp`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['usage_percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the moveset ability chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`month`,
				`mra`.`percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_abilities` AS `mra`
				ON `urp`.`id` = `mra`.`usage_rated_pokemon_id`
			WHERE `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `mra`.`ability_id` = :ability_id
			ORDER BY `urp`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the moveset item chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`month`,
				`mri`.`percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_items` AS `mri`
				ON `urp`.`id` = `mri`.`usage_rated_pokemon_id`
			WHERE `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `mri`.`item_id` = :item_id
			ORDER BY `urp`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the moveset move chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getMovesetMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`urp`.`month`,
				`mrm`.`percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_moves` AS `mrm`
				ON `urp`.`id` = `mrm`.`usage_rated_pokemon_id`
			WHERE `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
				AND `mrm`.`move_id` = :move_id
			ORDER BY `urp`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the usage ability chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsageAbility(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`month`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_abilities` AS `m`
				ON `u`.`id` = `m`.`usage_rated_pokemon_id`
			WHERE `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `u`.`pokemon_id` = :pokemon_id
				AND `m`.`ability_id` = :ability_id
			ORDER BY `u`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['usage_percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the usage item chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsageItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`month`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_items` AS `m`
				ON `u`.`id` = `m`.`usage_rated_pokemon_id`
			WHERE `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `u`.`pokemon_id` = :pokemon_id
				AND `m`.`item_id` = :item_id
			ORDER BY `u`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['usage_percent'];
		}

		return $usageDatas;
	}

	/**
	 * Get usage data for the usage move chart.
	 *
	 * @return float[] Indexed by month ('YYYY-MM-DD'). Ordered by month.
	 */
	public function getUsageMove(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`month`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_moves` AS `m`
				ON `u`.`id` = `m`.`usage_rated_pokemon_id`
			WHERE `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `u`.`pokemon_id` = :pokemon_id
				AND `m`.`move_id` = :move_id
			ORDER BY `u`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageDatas = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageDatas[$result['month']] = (float) $result['usage_percent'];
		}

		return $usageDatas;
	}
}
