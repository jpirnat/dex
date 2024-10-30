<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\Flags\DexMoveFlag;
use Jp\Dex\Domain\Moves\Flags\MoveFlag;
use Jp\Dex\Domain\Moves\Flags\MoveFlagId;
use Jp\Dex\Domain\Moves\Flags\MoveFlagNotFoundException;
use Jp\Dex\Domain\Moves\Flags\MoveFlagRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseMoveFlagRepository implements MoveFlagRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a move flag by its identifier.
	 *
	 * @throws MoveFlagNotFoundException if no move flag exists with this
	 *     identifier.
	 */
	public function getByIdentifier(string $identifier) : MoveFlag
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`
			FROM `move_flags`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MoveFlagNotFoundException(
				"No move flag exists with identifier $identifier."
			);
		}

		return new MoveFlag(
			new MoveFlagId($result['id']),
			$identifier,
		);
	}

	/**
	 * Get all dex move flags in this version group, with descriptions in
	 * plural form. ("These moves")
	 *
	 * @return DexMoveFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupPlural(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		// HACK: Move flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`id`,
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description_plural`
			FROM `move_flags` AS `f`
			INNER JOIN `vg_move_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `move_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
				AND `vgf`.`is_functional` = 1
				AND `fd`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$flags = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flag = new DexMoveFlag(
				$result['identifier'],
				$result['name'],
				$result['description_plural'],
			);

			$flags[$result['id']] = $flag;
		}

		return $flags;
	}

	/**
	 * Get a dex move flag, with description in plural form.
	 */
	public function getByIdPlural(
		VersionGroupId $versionGroupId,
		MoveFlagId $flagId,
		LanguageId $languageId,
	) : DexMoveFlag {
		// HACK: Move flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description_plural`
			FROM `move_flags` AS `f`
			INNER JOIN `vg_move_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `move_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
				AND `vgf`.`flag_id` = :flag_id
				AND `fd`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':flag_id', $flagId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return new DexMoveFlag(
			$result['identifier'],
			$result['name'],
			$result['description_plural'],
		);
	}

	/**
	 * Get all dex move flags in this version group, with descriptions in
	 * singular form. ("This move")
	 *
	 * @return DexMoveFlag[] Indexed by flag id.
	 */
	public function getByVersionGroupSingular(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		// HACK: Move flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`id`,
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description_singular`
			FROM `move_flags` AS `f`
			INNER JOIN `vg_move_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `move_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
				AND `vgf`.`is_functional` = 1
				AND `fd`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$flags = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flag = new DexMoveFlag(
				$result['identifier'],
				$result['name'],
				$result['description_singular'],
			);

			$flags[$result['id']] = $flag;
		}

		return $flags;
	}

	/**
	 * Get this move's flags.
	 *
	 * @return MoveFlagId[] Indexed by flag id.
	 */
	public function getByMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`flag_id`
			FROM `vg_moves_flags`
			WHERE `version_group_id` = :version_group_id
				AND `move_id` = :move_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$flagIds = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flagId = new MoveFlagId($result['flag_id']);

			$flagIds[$result['flag_id']] = $flagId;
		}

		return $flagIds;
	}
}
