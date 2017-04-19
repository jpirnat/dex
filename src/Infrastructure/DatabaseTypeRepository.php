<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Moves\CategoryId;
use Jp\Dex\Domain\Types\Type;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use PDO;

class DatabaseTypeRepository implements TypeRepositoryInterface
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
	 * Get a type by its hidden power index.
	 *
	 * @param int $hiddenPowerIndex
	 *
	 * @throws Exception if no type exists with this hidden power index.
	 *
	 * @return Type
	 */
	public function getByHiddenPowerIndex(int $hiddenPowerIndex) : Type
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`category_id`
			FROM `types`
			WHERE `hidden_power_index` = :hidden_power_index
			LIMIT 1'
		);
		$stmt->bindValue(':hidden_power_index', $hiddenPowerIndex, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new Exception('No type exists with hidden power index ' . $hiddenPowerIndex);
		}

		if ($result['category_id'] !== null) {
			// The type had a damage category.
			$categoryId = new CategoryId($result['category_id']);
		} else {
			$categoryId = null;
		}

		$type = new Type(
			new TypeId($result['id']),
			$result['identifier'],
			$categoryId,
			$hiddenPowerIndex
		);

		return $type;
	}
}
