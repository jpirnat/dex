<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\CategoryId;
use Jp\Dex\Domain\Moves\GenerationMove;
use Jp\Dex\Domain\Moves\GenerationMoveNotFoundException;
use Jp\Dex\Domain\Moves\GenerationMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\Inflictions\InflictionId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\Qualities\QualityId;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Moves\ZPowerEffects\ZPowerEffectId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseGenerationMoveRepository implements GenerationMoveRepositoryInterface
{
	/** @var PDO $db */
	private $db;

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
	 * @param Generation $generation
	 * @param MoveId $moveId
	 *
	 * @throws GenerationMoveNotFoundException if no generation move exists with
	 *     this generation and move.
	 *
	 * @return GenerationMove
	 */
	public function getByGenerationAndMove(
		Generation $generation,
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
				`z_power_effect_id`
			FROM `generation_moves`
			WHERE `generation` = :generation
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation', $generation->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new GenerationMoveNotFoundException(
				'No generation move exists with generation '
				. $generation->value() . ' and move id ' . $moveId->value()
				. '.'
			);
		}

		if ($result['quality_id'] !== null) {
			// The generation move has a quality.
			$qualityId = new QualityId($result['quality_id']);
		} else {
			$qualityId = null;
		}

		if ($result['infliction_id'] !== null) {
			// The generation move has an infliction.
			$inflictionId = new InflictionId($result['infliction_id']);
		} else {
			$inflictionId = null;
		}

		if ($result['z_move_id'] !== null) {
			// The generation move has a Z-Move.
			$zMoveId = new MoveId($result['z_move_id']);
		} else {
			$zMoveId = null;
		}

		if ($result['z_power_effect_id'] !== null) {
			// The generation move has a Z-Power Effect.
			$zPowerEffectId = new ZPowerEffectId($result['z_power_effect_id']);
		} else {
			$zPowerEffectId = null;
		}

		$generationMove = new GenerationMove(
			$generation,
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
			$zPowerEffectId
		);

		return $generationMove;
	}
}
