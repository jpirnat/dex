<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories;

use PDO;

class ItemsRepository
{
	/** @var PDO $db */
	protected $db;

	/** @var array $itemIds */
	protected $itemIds;

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
			FROM `items`'
		);
		$stmt->execute();
		$this->itemIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Insert an item into the database.
	 *
	 * @param string $name
	 *
	 * @return int The item's id
	 */
	protected function insertItem(string $name) : int
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `items` (
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
	 * Get the id of an item name.
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	protected function getItemId(string $name) : int
	{
		if (!isset($this->itemIds[$name])) {
			$this->itemIds[$name] = $this->insertItem($name);
		}

		return $this->itemIds[$name];
	}
}
