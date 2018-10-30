<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\CategoryId;
use Jp\Dex\Domain\Types\Type;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNotFoundException;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use PDO;

class DatabaseTypeRepository implements TypeRepositoryInterface
{
	/** @var PDO $db */
	private $db;

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
	 * Get a type by its id.
	 *
	 * @param TypeId $typeId
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 *
	 * @return Type
	 */
	public function getById(TypeId $typeId) : Type
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`category_id`,
				`hidden_power_index`,
				`color_code`
			FROM `types`
			WHERE `id` = :type_id
			LIMIT 1'
		);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeNotFoundException(
				'No type exists with id ' . $typeId->value() . '.'
			);
		}

		$categoryId = $result['category_id'] !== null
			? new CategoryId($result['category_id'])
			: null;

		$type = new Type(
			$typeId,
			$result['identifier'],
			$categoryId,
			$result['hidden_power_index'],
			$result['color_code']
		);

		return $type;
	}

	/**
	 * Get a type by its hidden power index.
	 *
	 * @param int $hiddenPowerIndex
	 *
	 * @throws TypeNotFoundException if no type exists with this hidden power
	 *     index.
	 *
	 * @return Type
	 */
	public function getByHiddenPowerIndex(int $hiddenPowerIndex) : Type
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`category_id`,
				`color_code`
			FROM `types`
			WHERE `hidden_power_index` = :hidden_power_index
			LIMIT 1'
		);
		$stmt->bindValue(':hidden_power_index', $hiddenPowerIndex, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeNotFoundException(
				'No type exists with hidden power index ' . $hiddenPowerIndex
			);
		}

		$categoryId = $result['category_id'] !== null
			? new CategoryId($result['category_id'])
			: null;

		$type = new Type(
			new TypeId($result['id']),
			$result['identifier'],
			$categoryId,
			$hiddenPowerIndex,
			$result['color_code']
		);

		return $type;
	}

	/**
	 * Get the main types.
	 *
	 * @return Type[] Indexed by id.
	 */
	public function getMain() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`hidden_power_index`,
				`category_id`,
				`color_code`
			FROM `types`
			WHERE `id` < 100'
		);
		$stmt->execute();

		$types = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$categoryId = $result['category_id'] !== null
				? new CategoryId($result['category_id'])
				: null;

			$type = new Type(
				new TypeId($result['id']),
				$result['identifier'],
				$categoryId,
				$result['hidden_power_index'],
				$result['color_code']
			);

			$types[$result['id']] = $type;
		}

		return $types;
	}
}
