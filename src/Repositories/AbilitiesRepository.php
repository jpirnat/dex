<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories;

use PDO;

class AbilitiesRepository
{
	/** @var PDO $db */
	protected $db;

	/** @var array $abilityIds */
	protected $abilityIds;

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
			FROM `abilities`'
		);
		$stmt->execute();
		$this->abilityIds = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Insert an ability into the database.
	 *
	 * @param string $name
	 *
	 * @return int The ability's id
	 */
	protected function insertAbility(string $name) : int
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `abilities` (
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
	 * Get the id of an ability name.
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function getAbilityId(string $name) : int
	{
		if (!isset($this->abilityIds[$name])) {
			$this->abilityIds[$name] = $this->insertAbility($name);
		}

		return $this->abilityIds[$name];
	}
}
