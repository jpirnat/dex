<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\EggGroups\EggGroup;
use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\EggGroups\EggGroupNotFoundException;
use Jp\Dex\Domain\EggGroups\EggGroupRepositoryInterface;
use PDO;

final readonly class DatabaseEggGroupRepository implements EggGroupRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an egg group by its identifier.
	 *
	 * @throws EggGroupNotFoundException if no egg group exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : EggGroup
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`
			FROM `egg_groups`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new EggGroupNotFoundException(
				"No egg group exists with identifier $identifier."
			);
		}

		return new EggGroup(
			new EggGroupId($result['id']),
			$identifier,
		);
	}
}
