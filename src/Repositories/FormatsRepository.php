<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories;

use PDO;

class FormatsRepository
{
	/** @var PDO $db */
	protected $db;

	/** @var array $smogonFormatNameIds */
	protected $smogonFormatNameIds;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;

		$stmt = $this->db->prepare(
			'SELECT
				`name`,
				`format_id`
			FROM `smogon_format_names`'
		);
		$stmt->execute();
		$this->smogonFormatNameIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	public function hasSmogonFormatName(string $smogonFormatName) : bool
	{
		return isset($this->smogonFormatNameIds[$smogonFormatName]);
	}

	/**
	 * Get the format id of a Smogon format name.
	 *
	 * @param string $smogonFormatName
	 *
	 * @throws Exception if $smogonFormatName is an unknown format
	 *
	 * @return int
	 */
	public function getFormatId(string $smogonFormatName) : int
	{
		if (!$this->hasSmogonFormatName($smogonFormatName)) {
			throw new Exception('Format is not known.');
		}

		return $this->smogonFormatNameIds[$smogonFormatName];
	}
}
