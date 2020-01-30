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
	 * Get a generation by its id.
	 *
	 * @param GenerationId $generationId
	 *
	 * @throws GenerationNotFoundException if no generation exists with this id.
	 *
	 * @return Generation
	 */
	public function getById(GenerationId $generationId) : Generation
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`icon`
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
			$result['icon']
		);

		return $generation;
	}

	/**
	 * Get a generation by its identifier
	 *
	 * @param string $identifier
	 *
	 * @throws GenerationNotFoundException if no generation exists with this
	 *     identifier.
	 *
	 * @return Generation
	 */
	public function getByIdentifier(string $identifier) : Generation
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`icon`
			FROM `generations`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new GenerationNotFoundException(
				"No generation exists with identifier $identifier."
			);
		}

		$generation = new Generation(
			new GenerationId($result['id']),
			$identifier,
			$result['icon']
		);

		return $generation;
	}

	/**
	 * Get generations since the given generation, inclusive.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return Generation[] Indexed by id. Ordered by id.
	 */
	public function getSince(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`icon`
			FROM `generations`
			WHERE `id` >= :generation_id
			ORDER BY `id`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$generations = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$generation = new Generation(
				new GenerationId($result['id']),
				$result['identifier'],
				$result['icon']
			);

			$generations[$result['id']] = $generation;
		}

		return $generations;
	}
}
