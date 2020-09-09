<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\GenerationMove;
use Jp\Dex\Domain\Moves\GenerationMoveNotFoundException;
use Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\Qualities\QualityId;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Moves\ZPowerEffects\ZPowerEffectId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseGenerationMoveRepository implements GenerationMoveRepositoryInterface
{
	private PDO $db;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Get a generation move by its generation and move.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 *
	 * @throws GenerationMoveNotFoundException if no generation move exists with
	 *     this generation and move.
	 *
	 * @return GenerationMove
	 */
	public function getByGenerationAndMove(
		GenerationId $generationId,
		MoveId $moveId
	) : GenerationMove {
		$stmt = $this->db->prepare(
			'SELECT
				`type_id`,
				`quality_id`,
				`category_id`,
				`power`,
				`accuracy`,
				`pp`,
				`priority`,
				`min_hits`,
				`max_hits`,
				`infliction_id`,
				`infliction_percent`,
				`min_turns`,
				`max_turns`,
				`crit_stage`,
				`flinch_percent`,
				`effect`,
				`effect_percent`,
				`recoil_percent`,
				`heal_percent`,
				`target_id`,
				`z_move_id`,
				`z_base_power`,
				`z_power_effect_id`,
				`max_move_id`,
				`max_power`
			FROM `generation_moves`
			WHERE `generation_id` = :generation_id
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new GenerationMoveNotFoundException(
				'No generation move exists with generation id '
				. $generationId->value() . ' and move id ' . $moveId->value()
				. '.'
			);
		}

		$qualityId = $result['quality_id'] !== null
			? new QualityId($result['quality_id'])
			: null;
		$inflictionId = $result['infliction_id'] !== null
			? new InflictionId($result['infliction_id'])
			: null;
		$zMoveId = $result['z_move_id'] !== null
			? new MoveId($result['z_move_id'])
			: null;
		$zPowerEffectId = $result['z_power_effect_id'] !== null
			? new ZPowerEffectId($result['z_power_effect_id'])
			: null;
		$maxMoveId = $result['max_move_id'] !== null
			? new MoveId($result['max_move_id'])
			: null;

		$generationMove = new GenerationMove(
			$generationId,
			$moveId,
			new TypeId($result['type_id']),
			$qualityId,
			new CategoryId($result['category_id']),
			$result['power'],
			$result['accuracy'],
			$result['pp'],
			$result['priority'],
			$result['min_hits'],
			$result['max_hits'],
			$inflictionId,
			$result['infliction_percent'],
			$result['min_turns'],
			$result['max_turns'],
			$result['crit_stage'],
			$result['flinch_percent'],
			$result['effect'],
			$result['effect_percent'],
			$result['recoil_percent'],
			$result['heal_percent'],
			new TargetId($result['target_id']),
			$zMoveId,
			$result['z_base_power'],
			$zPowerEffectId,
			$maxMoveId,
			$result['max_power']
		);

		return $generationMove;
	}

	/**
	 * Get the infliction.
	 *
	 * @param InflictionId $inflictionId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getInfliction(InflictionId $inflictionId, LanguageId $languageId) : array
	{
		// HACK: Move infliction names currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`i`.`identifier`,
				`in`.`name`
			FROM `inflictions` AS `i`
			INNER JOIN `infliction_names` AS `in`
				ON `i`.`id` = `in`.`infliction_id`
			WHERE `i`.`id` = :infliction_id
				AND `in`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':infliction_id', $inflictionId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get the target.
	 *
	 * @param TargetId $targetId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getTarget(TargetId $targetId, LanguageId $languageId) : array
	{
		// HACK: Move target names currently exist only for English.
		$languageId = new LanguageId(LanguageId::ENGLISH);

		$stmt = $this->db->prepare(
			'SELECT
				`t`.`identifier`,
				`tn`.`name`
			FROM `targets` AS `t`
			INNER JOIN `target_names` AS `tn`
				ON `t`.`id` = `tn`.`target_id`
			WHERE `t`.`id` = :target_id
				AND `tn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':target_id', $targetId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get the Z-Move.
	 *
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getZMove(MoveId $moveId, LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`
			FROM `moves` AS `m`
			INNER JOIN `z_move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			WHERE `m`.`id` = :move_id
				AND `mn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get the Max Move.
	 *
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getMaxMove(MoveId $moveId, LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			WHERE `m`.`id` = :move_id
				AND `mn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get the Z-Power Effect.
	 *
	 * @param ZPowerEffectId $zPowerEffectId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getZPowerEffect(ZPowerEffectId $zPowerEffectId, LanguageId $languageId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`z`.`identifier`,
				`zn`.`name`
			FROM `z_power_effects` AS `z`
			INNER JOIN `z_power_effect_names` AS `zn`
				ON `z`.`id` = `zn`.`z_power_effect_id`
			WHERE `z`.`id` = :z_power_effect_id
				AND `zn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':z_power_effect_id', $zPowerEffectId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get the move's stat changes.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return array
	 */
	public function getStatChanges(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`sn`.`name` AS `statName`,
				`msc`.`stages`,
				`msc`.`percent`
			FROM `move_stat_changes` AS `msc`
			INNER JOIN `stat_names` AS `sn`
				ON `msc`.`stat_id` = `sn`.`stat_id`
			WHERE `msc`.`generation_id` = :generation_id
				AND `msc`.`move_id` = :move_id
				AND `sn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
