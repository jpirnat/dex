<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use PDO;

final class DatabaseDexVersionGroupRepository implements DexVersionGroupRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a dex version group by its id.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		LanguageId $languageId
	) : DexVersionGroup {
		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`id`,
				`vg`.`identifier`,
				`vg`.`generation_id`,
				`vg`.`icon`,
				`vgn`.`name`
			FROM `version_groups` AS `vg`
			INNER JOIN `version_group_names` AS `vgn`
				ON `vg`.`id` = `vgn`.`version_group_id`
			WHERE `vg`.`id` = :version_group_id
				AND `vgn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VersionGroupNotFoundException(
				'No version group exists with id ' . $versionGroupId->value() . '.'
			);
		}

		return new DexVersionGroup(
			new VersionGroupId($result['id']),
			$result['identifier'],
			new GenerationId($result['generation_id']),
			$result['icon'],
			$result['name']
		);
	}

	/**
	 * Get dex version groups that this Pokémon has appeared in, up to a certain
	 * generation. This method is used to get all relevant version groups for
	 * the dex Pokémon page.
	 *
	 * @return DexVersionGroup[] Indexed by id. Ordered by sort.
	 */
	public function getWithPokemon(
		PokemonId $pokemonId,
		LanguageId $languageId,
		GenerationId $end
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`id`,
				`vg`.`identifier`,
				`vg`.`generation_id`,
				`vg`.`icon`,
				`vgn`.`name`
			FROM `version_groups` AS `vg`
			INNER JOIN `version_group_names` AS `vgn`
				ON `vg`.`id` = `vgn`.`version_group_id`
			WHERE `vg`.`id` IN (
				SELECT
					`version_group_id`
				FROM `version_group_pokemon`
				WHERE `pokemon_id` = :pokemon_id
			)
			AND `vgn`.`language_id` = :language_id
			AND `vg`.`generation_id` <= :end
			ORDER BY `vg`.`sort`'
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':end', $end->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$vg = new DexVersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['icon'],
				$result['name']
			);

			$versionGroups[$result['id']] = $vg;
		}

		// Use the appropriate set of gen 1 games for the language.
		if ($languageId->isJapanese()) {
			unset($versionGroups[VersionGroupId::RED_BLUE]);
		} else {
			unset($versionGroups[VersionGroupId::RED_GREEN]);
			unset($versionGroups[VersionGroupId::BLUE]);
		}

		return $versionGroups;
	}

	/**
	 * Get dex version groups that this move has appeared in, up to a certain
	 * generation. This method is used to get all relevant version groups for
	 * the dex move page.
	 *
	 * @return DexVersionGroup[] Indexed by id. Ordered by sort.
	 */
	public function getWithMove(
		MoveId $moveId,
		LanguageId $languageId,
		GenerationId $end
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`id`,
				`vg`.`identifier`,
				`vg`.`generation_id`,
				`vg`.`icon`,
				`vgn`.`name`
			FROM `version_groups` AS `vg`
			INNER JOIN `version_group_names` AS `vgn`
				ON `vg`.`id` = `vgn`.`version_group_id`
			WHERE `vg`.`id` IN (
				SELECT
					`version_group_id`
				FROM `version_group_moves`
				WHERE `move_id` = :move_id
			)
			AND `vgn`.`language_id` = :language_id
			AND `vg`.`generation_id` <= :end
			ORDER BY `vg`.`sort`'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':end', $end->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$vg = new DexVersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['icon'],
				$result['name']
			);

			$versionGroups[$result['id']] = $vg;
		}

		// Use the appropriate set of gen 1 games for the language.
		if ($languageId->isJapanese()) {
			unset($versionGroups[VersionGroupId::RED_BLUE]);
		} else {
			unset($versionGroups[VersionGroupId::RED_GREEN]);
			unset($versionGroups[VersionGroupId::BLUE]);
		}

		return $versionGroups;
	}
}
