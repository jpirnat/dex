<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseFormatRepository
{
	/** @var PDO $db */
	protected $db;

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
	 * Get a format by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no format exists with this identifier.
	 *
	 * @return Format
	 */
	public function getByIdentifier(string $identifier) : Format
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`generation`,
				`level`,
				`field_size`,
				`team_size`,
				`in_battle_team_size`
			FROM `formats`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new Exception('No format exists with identifier ' . $identifier);
		}

		$format = new Format(
			new FormatId($result['id']),
			$identifier,
			new Generation($result['generation']),
			$result['level'],
			$result['field_size'],
			$result['team_size'],
			$result['in_battle_team_size']
		);

		return $format;
	}
}
