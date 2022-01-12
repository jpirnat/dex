<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Species\Species;
use Jp\Dex\Domain\Species\SpeciesId;
use Jp\Dex\Domain\Species\SpeciesNotFoundException;
use Jp\Dex\Domain\Species\SpeciesRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseSpeciesRepository implements SpeciesRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a species by its id.
	 *
	 * @throws SpeciesNotFoundException if no species exists with this id.
	 */
	public function getById(SpeciesId $speciesId) : Species
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`introduced_in_version_group_id`,
				`base_egg_cycles`
			FROM `species`
			WHERE `id` = :species_id
			LIMIT 1'
		);
		$stmt->bindValue(':species_id', $speciesId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new SpeciesNotFoundException(
				'No species exists with id ' . $speciesId->value() . '.'
			);
		}

		$species = new Species(
			$speciesId,
			$result['identifier'],
			new VersionGroupId($result['introduced_in_version_group_id']),
			$result['base_egg_cycles']
		);

		return $species;
	}
}
