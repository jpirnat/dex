<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories;

use PDO;

class MovesRepository
{
	/** @var PDO $db */
	protected $db;

	/** @var array $moveIds */
	protected $moveIds;

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
			FROM `moves`'
		);
		$stmt->execute();
		$this->moveIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Insert a move into the database.
	 *
	 * @param string $name
	 *
	 * @return int The move's id
	 */
	protected function insertMove(string $name) : int
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moves` (
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
	 * Get the id of a move name.
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function getMoveId(string $name) : int
	{
		if (!isset($this->moveIds[$name])) {
			$this->moveIds[$name] = $this->insertMove($name);
		}

		return $this->moveIds[$name];
	}
}
