<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Characteristics\Characteristic;
use Jp\Dex\Domain\Characteristics\CharacteristicId;
use Jp\Dex\Domain\Characteristics\CharacteristicNotFoundException;
use Jp\Dex\Domain\Characteristics\CharacteristicRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use PDO;

final readonly class DatabaseCharacteristicRepository implements CharacteristicRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a characteristic by its identifier.
	 *
	 * @throws CharacteristicNotFoundException if no characteristic exists with
	 *     this identifier.
	 */
	public function getByIdentifier(string $identifier) : Characteristic
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`highest_stat_id`,
				`iv_mod_five`
			FROM `characteristics`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new CharacteristicNotFoundException(
				"No characteristic exists with identifier $identifier."
			);
		}

		return new Characteristic(
			new CharacteristicId($result['id']),
			$identifier,
			new StatId($result['highest_stat_id']),
			$result['iv_mod_five'],
		);
	}
}
