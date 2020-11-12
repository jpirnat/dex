<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Flags\DexFlag;
use Jp\Dex\Domain\Flags\FlagId;
use Jp\Dex\Domain\Flags\FlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseFlagRepository implements FlagRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get all dex flags in this generation.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexFlag[] Indexed by flag id.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		// HACK: Move flag descriptions currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`f`.`id`,
				`f`.`identifier`,
				`fd`.`name`,
				`fd`.`description`
			FROM `flags` AS `f`
			INNER JOIN `generation_flags` AS `gf`
				ON `f`.`id` = `gf`.`flag_id`
			INNER JOIN `flag_descriptions` AS `fd`
				ON `gf`.`flag_id` = `fd`.`flag_id`
			WHERE `gf`.`generation_id` = :generation_id
				AND `fd`.`language_id` = :language_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$flags = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flag = new DexFlag(
				$result['identifier'],
				$result['name'],
				$result['description']
			);

			$flags[$result['id']] = $flag;
		}

		return $flags;
	}

	/**
	 * Get this move's flags.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 *
	 * @return FlagId[] Indexed by flag id.
	 */
	public function getByMove(
		GenerationId $generationId,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`flag_id`
			FROM `move_flags`
			WHERE `generation_id` = :generation_id
				AND `move_id` = :move_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$flagIds = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flagId = new FlagId($result['flag_id']);

			$flagIds[$result['flag_id']] = $flagId;
		}

		return $flagIds;
	}
}
