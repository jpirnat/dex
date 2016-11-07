<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories;

use PDO;

class NaturesRepository
{
	/** @var PDO $db */
	protected $db;

	/** @var array $natureIds */
	protected $natureIds;

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
				`id`
			FROM `natures`'
		);
		$stmt->execute();
		$this->natureIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Insert a nature into the database.
	 *
	 * @param string $name
	 *
	 * @return int The nature's id
	 */
	protected function insertNature(string $name) : int
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `natures` (
				`name`
			) VALUES (
				:name
			)'
		);
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->execute();
		return (int) $this->db->lastInsertId();
	}

	/**
	 * Get the id of a nature name.
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function getNatureId(string $name) : int
	{
		if (!isset($this->natureIds[$name])) {
			$this->natureIds[$name] = $this->insertNature($name);
		}

		return $this->natureIds[$name];
	}
}
