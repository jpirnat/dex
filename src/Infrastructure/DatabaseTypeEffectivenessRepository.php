<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Types\TypeEffectiveness;
use Jp\Dex\Domain\Types\TypeEffectivenessRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseTypeEffectivenessRepository implements TypeEffectivenessRepositoryInterface
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
	 * Get type effectivenesses by generation.
	 *
	 * @param Generation $generation
	 *
	 * @return TypeEffectiveness[]
	 */
	public function getByGeneration(Generation $generation) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`attacking_type_id`,
				`defending_type_id`,
				`factor`
			FROM `type_charts`
			WHERE `generation` = :generation'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->execute();

		$typeEffectivenesses = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$typeEffectiveness = new TypeEffectiveness(
				$generation,
				new TypeId($result['attacking_type_id']),
				new TypeId($result['defending_type_id']),
				(float) $result['factor']
			);

			$typeEffectivenesses[] = $typeEffectiveness;
		}

		return $typeEffectivenesses;
	}
}
