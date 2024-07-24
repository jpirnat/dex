<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMoveFlag;
use Jp\Dex\Domain\Moves\MoveFlagId;
use Jp\Dex\Domain\Moves\MoveFlagRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseMoveFlagRepository implements MoveFlagRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get all dex move flags in this version group.
	 *
	 * @return DexMoveFlag[] Indexed by flag id.
	 */
	public function getByVersionGroup(
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
				`fd`.`description`
			FROM `move_flags` AS `f`
			INNER JOIN `vg_move_flags` AS `vgf`
				ON `f`.`id` = `vgf`.`flag_id`
			INNER JOIN `move_flag_descriptions` AS `fd`
				ON `vgf`.`version_group_id` = `fd`.`version_group_id`
				AND `vgf`.`flag_id` = `fd`.`flag_id`
			WHERE `vgf`.`version_group_id` = :version_group_id
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
				$result['description'],
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
