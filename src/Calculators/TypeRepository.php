<?php
declare(strict_types=1);

namespace Jp\Dex\Repositories;

use PDO;

class TypeRepository implements TypeRepositoryInterface
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
	 * Get 
	 *
	 * @param int $hiddenPowerIndex
	 *
	 * @return Type
	 */
	public function getByHiddenPowerIndex(int $index)
	{
		$stmt = $this->db->prepare(
			'SELECT
				`type_id`
			FROM `hidden_power_type_indexes`
			WHERE `index` = :index
			LIMIT 1'
		);
		$stmt->bindValue(':index', $index, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		// TODO: Type entity
	}
}
