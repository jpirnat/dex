<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseDexTypeRepository implements DexTypeRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a dex type by its id.
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 */
	public function getById(
		TypeId $typeId,
		LanguageId $languageId,
	) : DexType {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `types` AS `t`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `t`.`id` = :type_id
				AND `tn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeNotFoundException(
				'No type exists with id ' . $typeId->value() . '.'
			);
		}

		return new DexType(
			$result['identifier'],
			$result['name'],
			$result['icon'] ?? '',
		);
	}

	/**
	 * Get the main dex types available in this version group.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getMainByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `types` AS `t`
			INNER JOIN `vg_types` AS `vgt`
				ON `t`.`id` = `vgt`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `t`.`id` < 18
				AND `vgt`.`version_group_id` = :version_group_id
				AND `tn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				$result['identifier'],
				$result['name'],
				$result['icon'] ?? '',
			);

			$dexTypes[$result['id']] = $dexType;
		}

		return $dexTypes;
	}

	/**
	 * Get the dex types available in this version group.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `types` AS `t`
			INNER JOIN `vg_types` AS `vgt`
				ON `t`.`id` = `vgt`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `vgt`.`version_group_id` = :version_group_id
				AND `tn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				$result['identifier'],
				$result['name'],
				$result['icon'] ?? '',
			);

			$dexTypes[$result['id']] = $dexType;
		}

		return $dexTypes;
	}
}
