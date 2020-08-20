<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Types\TypeMatchup;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseTypeMatchupRepository implements TypeMatchupRepositoryInterface
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
	 * Get type matchups by generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return TypeMatchup[]
	 */
	public function getByGeneration(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`attacking_type_id`,
				`defending_type_id`,
				`multiplier`
			FROM `type_matchups`
			WHERE `generation_id` = :generation_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeMatchups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeMatchup = new TypeMatchup(
				$generationId,
				new TypeId($result['attacking_type_id']),
				new TypeId($result['defending_type_id']),
				(float) $result['multiplier']
			);

			$typeMatchups[] = $typeMatchup;
		}

		return $typeMatchups;
	}

	/**
	 * Get type matchups by generation and attacking type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 *
	 * @return TypeMatchup[]
	 */
	public function getByAttackingType(GenerationId $generationId, TypeId $typeId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`defending_type_id`,
				`multiplier`
			FROM `type_matchups`
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
				$typeId,
				new TypeId($result['defending_type_id']),
				(float) $result['multiplier']
			);

			$typeMatchups[] = $typeMatchup;
		}

		return $typeMatchups;
	}

	/**
	 * Get type matchups by generation and defending type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 *
	 * @return TypeMatchup[]
	 */
	public function getByDefendingType(GenerationId $generationId, TypeId $typeId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`attacking_type_id`,
				`multiplier`
			FROM `type_matchups`
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
				new TypeId($result['attacking_type_id']),
				$typeId,
				(float) $result['multiplier']
			);

			$typeMatchups[] = $typeMatchup;
		}

		return $typeMatchups;
	}
}
