<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlag;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlagId;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlagNotFoundException;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlagRepositoryInterface;
use Jp\Dex\Domain\Abilities\Flags\DexAbilityFlag;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseAbilityFlagRepository implements AbilityFlagRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an ability flag by its identifier.
	 *
	 * @throws AbilityFlagNotFoundException if no ability flag exists with this
	 *     identifier.
	 */
	public function getByIdentifier(string $identifier) : AbilityFlag
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`
			FROM `ability_flags`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new AbilityFlagNotFoundException(
				"No ability flag exists with identifier $identifier."
			);
		}

		return new AbilityFlag(
			new AbilityFlagId($result['id']),
			$identifier,
		);
	}

	/**
	 * Get all dex ability flags in this version group, with descriptions in
	 * plural form. ("These abilities")
	 *
	 * @return DexAbilityFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupPlural(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		// HACK: Ability flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`id`,
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description_plural`
			FROM `ability_flags` AS `f`
			INNER JOIN `vg_ability_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `ability_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
				AND `vgf`.`is_functional` = 1
				AND `fd`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$flags = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flag = new DexAbilityFlag(
				$result['identifier'],
				$result['name'],
				$result['description_plural'],
			);

			$flags[$result['id']] = $flag;
		}

		return $flags;
	}

	/**
	 * Get a dex ability flag, with description in plural form.
	 */
	public function getByIdPlural(
		VersionGroupId $versionGroupId,
		AbilityFlagId $flagId,
		LanguageId $languageId,
	) : DexAbilityFlag {
		// HACK: Ability flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description_plural`
			FROM `ability_flags` AS `f`
			INNER JOIN `vg_ability_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `ability_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
				AND `vgf`.`flag_id` = :flag_id
				AND `fd`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':flag_id', $flagId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return new DexAbilityFlag(
			$result['identifier'],
			$result['name'],
			$result['description_plural'],
		);
	}

	/**
	 * Get all dex ability flags in this version group, with descriptions in
	 * singular form. ("This ability")
	 *
	 * @return DexAbilityFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupSingular(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		// HACK: Ability flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`id`,
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description_singular`
			FROM `ability_flags` AS `f`
			INNER JOIN `vg_ability_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `ability_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
				AND `vgf`.`is_functional` = 1
				AND `fd`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();

		$flags = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flag = new DexAbilityFlag(
				$result['identifier'],
				$result['name'],
				$result['description_singular'],
			);

			$flags[$result['id']] = $flag;
		}

		return $flags;
	}

	/**
	 * Get this ability's flags.
	 *
	 * @return AbilityFlagId[] Indexed by flag id.
	 */
	public function getByAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`flag_id`
			FROM `vg_abilities_flags`
			WHERE `version_group_id` = :version_group_id
				AND `ability_id` = :ability_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value, PDO::PARAM_INT);
		$stmt->execute();

		$flagIds = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flagId = new AbilityFlagId($result['flag_id']);

			$flagIds[$result['flag_id']] = $flagId;
		}

		return $flagIds;
	}
}
