<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorPokemon;
use Jp\Dex\Application\Models\IvCalculator\IvCalculatorQueriesInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseIvCalculatorQueries implements IvCalculatorQueriesInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get Pokémon for the IV calculator page.
	 */
	public function getPokemons(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`p`.`identifier`,
				`pn`.`name`,
				`vp`.`sprite`,

				`t1`.`identifier` AS `type1_identifier`,
				`t1n`.`name` AS `type1_name`,
				`t1i`.`icon` AS `type1_icon`,

				`t2`.`identifier` AS `type2_identifier`,
				`t2n`.`name` AS `type2_name`,
				`t2i`.`icon` AS `type2_icon`,

				`vp`.`base_hp`,
				`vp`.`base_atk`,
				`vp`.`base_def`,
				`vp`.`base_spa`,
				`vp`.`base_spd`,
				`vp`.`base_spe`,
				`vp`.`base_spc`
			FROM `vg_pokemon` AS `vp`
			INNER JOIN `pokemon` AS `p`
				ON `vp`.`pokemon_id` = `p`.`id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`

			LEFT JOIN `types` AS `t1`
				ON `vp`.`type1_id` = `t1`.`id`
			LEFT JOIN `type_names` AS `t1n`
				ON `pn`.`language_id` = `t1n`.`language_id`
				AND `vp`.`type1_id` = `t1n`.`type_id`
			LEFT JOIN `type_icons` AS `t1i`
				ON `pn`.`language_id` = `t1i`.`language_id`
				AND `vp`.`type1_id` = `t1i`.`type_id`

			LEFT JOIN `types` AS `t2`
				ON `vp`.`type2_id` = `t2`.`id`
			LEFT JOIN `type_names` AS `t2n`
				ON `pn`.`language_id` = `t2n`.`language_id`
				AND `vp`.`type2_id` = `t2n`.`type_id`
			LEFT JOIN `type_icons` AS `t2i`
				ON `pn`.`language_id` = `t2i`.`language_id`
				AND `vp`.`type2_id` = `t2i`.`type_id`

			WHERE `vp`.`version_group_id` = :version_group_id
				AND `pn`.`language_id` = :language_id
			ORDER BY `name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$types = [];
			if ($result['type1_identifier']) {
				$types[] = new DexType(
					$result['type1_identifier'],
					$result['type1_name'],
					$result['type1_icon'],
				);
			}
			if ($result['type2_identifier']) {
				$types[] = new DexType(
					$result['type2_identifier'],
					$result['type2_name'],
					$result['type2_icon'],
				);
			}

			$baseStats = [];
			$bst = 0;

			$baseStats['hp'] = $result['base_hp'];
			$bst += $result['base_hp'];

			$baseStats['attack'] = $result['base_atk'];
			$bst += $result['base_atk'];

			$baseStats['defense'] = $result['base_def'];
			$bst += $result['base_def'];

			if ($result['base_spa']) {
				$baseStats['special-attack'] = $result['base_spa'];
				$bst += $result['base_spa'];
			}
			if ($result['base_spd']) {
				$baseStats['special-defense'] = $result['base_spd'];
				$bst += $result['base_spd'];
			}
			if ($result['base_spc']) {
				$baseStats['special'] = $result['base_spc'];
				$bst += $result['base_spc'];
			}
			$baseStats['speed'] = $result['base_spe'];
			$bst += $result['base_spe'];

			$pokemon =  new IvCalculatorPokemon(
				$result['identifier'],
				$result['name'],
				$result['sprite'] ?? '',
				$types,
				$baseStats,
				$bst,
			);

			$pokemons[] = $pokemon;
		}

		return $pokemons;
	}

	/**
	 * Get natures for the IV calculator page.
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
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get characteristics for the IV calculator page.
	 */
	public function getCharacteristics(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`c`.`identifier`,
				`cn`.`name`
			FROM `characteristics` AS `c`
			INNER JOIN `characteristic_names` AS `cn`
				ON `c`.`id` = `cn`.`characteristic_id`
			WHERE `cn`.`version_group_id` = :version_group_id
				AND `cn`.`language_id` = :language_id
			ORDER BY `name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get types for the IV calculator page.
	 */
	public function getTypes(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`identifier`,
				`tn`.`name`
			FROM `types` AS `t`
			INNER JOIN `vg_types` AS `vt`
				ON `t`.`id` = `vt`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			WHERE `vt`.`version_group_id` = :version_group_id
				AND `tn`.`language_id` = :language_id
				AND `t`.`hidden_power_index` IS NOT NULL
			ORDER BY `name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Get stats for the IV calculator page.
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
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
