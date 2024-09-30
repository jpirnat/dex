<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeMatchup;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final readonly class DatabaseTypeMatchupRepository implements TypeMatchupRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get type matchups by generation.
	 *
	 * @return TypeMatchup[]
	 */
	public function getByGeneration(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`a`.`identifier` AS `attacking_type_identifier`,
				`d`.`identifier` AS `defending_type_identifier`,
				`tm`.`multiplier`
			FROM `type_matchups` AS `tm`
			INNER JOIN `types` AS `a`
				ON `tm`.`attacking_type_id` = `a`.`id`
			INNER JOIN `types` AS `d`
				ON `tm`.`defending_type_id` = `d`.`id`
			WHERE `generation_id` = :generation_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeMatchups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeMatchup = new TypeMatchup(
				$generationId,
				$result['attacking_type_identifier'],
				$result['defending_type_identifier'],
				(float) $result['multiplier'],
			);

			$typeMatchups[] = $typeMatchup;
		}

		return $typeMatchups;
	}

	/**
	 * Get type matchups by generation and attacking type.
	 *
	 * @return TypeMatchup[]
	 */
	public function getByAttackingType(GenerationId $generationId, TypeId $typeId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`a`.`identifier` AS `attacking_type_identifier`,
				`d`.`identifier` AS `defending_type_identifier`,
				`multiplier`
			FROM `type_matchups` AS `tm`
			INNER JOIN `types` AS `a`
				ON `tm`.`attacking_type_id` = `a`.`id`
			INNER JOIN `types` AS `d`
				ON `tm`.`defending_type_id` = `d`.`id`
			WHERE `generation_id` = :generation_id
				AND `attacking_type_id` = :type_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeMatchups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeMatchup = new TypeMatchup(
				$generationId,
				$result['attacking_type_identifier'],
				$result['defending_type_identifier'],
				(float) $result['multiplier'],
			);

			$typeMatchups[] = $typeMatchup;
		}

		return $typeMatchups;
	}

	/**
	 * Get type matchups by generation and defending type.
	 *
	 * @return TypeMatchup[]
	 */
	public function getByDefendingType(GenerationId $generationId, TypeId $typeId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`a`.`identifier` AS `attacking_type_identifier`,
				`d`.`identifier` AS `defending_type_identifier`,
				`tm`.`multiplier`
			FROM `type_matchups` AS `tm`
			INNER JOIN `types` AS `a`
				ON `tm`.`attacking_type_id` = `a`.`id`
			INNER JOIN `types` AS `d`
				ON `tm`.`defending_type_id` = `d`.`id`
			WHERE `generation_id` = :generation_id
				AND `defending_type_id` = :type_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeMatchups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeMatchup = new TypeMatchup(
				$generationId,
				$result['attacking_type_identifier'],
				$result['defending_type_identifier'],
				(float) $result['multiplier'],
			);

			$typeMatchups[] = $typeMatchup;
		}

		return $typeMatchups;
	}
}
