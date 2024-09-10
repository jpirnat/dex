<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseDexStatRepository implements DexStatRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get stat names for this version group and language.
	 */
	public function getByVersionGroup(
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

	/**
	 * Get the base stats (with stat names) for this Pokémon.
	 */
	public function getBaseStats(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`s`.`id`,
				`s`.`identifier`,
				`sn`.`name`,
				`bs`.`value`
			FROM `stats` AS `s`
			INNER JOIN `stat_names` AS `sn`
				ON `s`.`id` = `sn`.`stat_id`
			INNER JOIN `base_stats` AS `bs`
				ON `s`.`id` = `bs`.`stat_id`
			WHERE `bs`.`version_group_id` = :version_group_id
				AND `bs`.`pokemon_id` = :pokemon_id
				AND `sn`.`language_id` = :language_id
			ORDER BY `s`.`sort`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
