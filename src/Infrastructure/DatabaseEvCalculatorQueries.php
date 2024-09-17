<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Application\Models\EvCalculator\EvCalculatorQueriesInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseEvCalculatorQueries implements EvCalculatorQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get Pokémon for the EV calculator page.
	 */
	public function getPokemons(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`p`.`identifier`,
				`pn`.`name`
			FROM `pokemon` AS `p`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `p`.`id` IN (
				SELECT DISTINCT
					`f`.`pokemon_id`
				FROM `vg_forms` AS `vgf`
				INNER JOIN `forms` AS `f`
					ON `vgf`.`form_id` = `f`.`id`
				WHERE `vgf`.`version_group_id` = :version_group_id
			)
				AND `pn`.`language_id` = :language_id
			ORDER BY `name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get natures for the EV calculator page.
	 */
	public function getNatures(LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`n`.`identifier`,
				`nn`.`name`,
				`sn1`.`abbreviation` AS `increasedStatAbbreviation`,
				`sn2`.`abbreviation` AS `decreasedStatAbbreviation`
			FROM `natures` AS `n`
			INNER JOIN `nature_names` AS `nn`
				ON `n`.`id` = `nn`.`nature_id`
			LEFT JOIN `stat_names` AS `sn1`
				ON `n`.`increased_stat_id` = `sn1`.`stat_id`
				AND `nn`.`language_id` = `sn1`.`language_id`
			LEFT JOIN `stat_names` AS `sn2`
				ON `n`.`decreased_stat_id` = `sn2`.`stat_id`
				AND `nn`.`language_id` = `sn2`.`language_id`
			WHERE `nn`.`language_id` = :language_id
			ORDER BY `name`'
		);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get stats for the EV calculator page.
	 */
	public function getStats(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`s`.`identifier`,
				`sn`.`name`,
				`sn`.`abbreviation`
			FROM `stats` AS `s`
			INNER JOIN `vg_stats` AS `vs`
				ON `s`.`id` = `vs`.`stat_id`
			INNER JOIN `stat_names` AS `sn`
				ON `s`.`id` = `sn`.`stat_id`
			WHERE `vs`.`version_group_id` = :version_group_id
				AND `sn`.`language_id` = :language_id
			ORDER BY `s`.`sort`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
