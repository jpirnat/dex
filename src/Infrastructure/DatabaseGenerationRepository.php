<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\GenerationNotFoundException;
use Jp\Dex\Domain\Versions\GenerationRepositoryInterface;
use PDO;

final class DatabaseGenerationRepository implements GenerationRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a generation by its id.
	 *
	 * @throws GenerationNotFoundException if no generation exists with this id.
	 */
	public function getById(GenerationId $generationId) : Generation
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`smogon_dex_identifier`
			FROM `generations`
			WHERE `id` = :generation_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new GenerationNotFoundException(
				'No generation exists with id ' . $generationId->value() . '.'
			);
		}

		$generation = new Generation(
			$generationId,
			$result['identifier'],
			$result['smogon_dex_identifier'],
		);

		return $generation;
	}
}
