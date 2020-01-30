<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Types\TypeEffectiveness;
use Jp\Dex\Domain\Types\TypeEffectivenessRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseTypeEffectivenessRepository implements TypeEffectivenessRepositoryInterface
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
	 * Get type effectivenesses by generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return TypeEffectiveness[]
	 */
	public function getByGeneration(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`attacking_type_id`,
				`defending_type_id`,
				`factor`
			FROM `type_effectivenesses`
			WHERE `generation_id` = :generation_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$typeEffectivenesses = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeEffectiveness = new TypeEffectiveness(
				$generationId,
				new TypeId($result['attacking_type_id']),
				new TypeId($result['defending_type_id']),
				(float) $result['factor']
			);

			$typeEffectivenesses[] = $typeEffectiveness;
		}

		return $typeEffectivenesses;
	}
}
