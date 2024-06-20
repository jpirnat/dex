<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Types\Type;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNotFoundException;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseTypeRepository implements TypeRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a type by its id.
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 */
	public function getById(TypeId $typeId) : Type
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`introduced_in_generation_id`,
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

		return new Type(
			$typeId,
			$result['identifier'],
			new GenerationId($result['introduced_in_generation_id']),
			$categoryId,
			$result['hidden_power_index'],
			$result['color_code'],
		);
	}

	/**
	 * Get a type by its identifier.
	 *
	 * @throws TypeNotFoundException if no type exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Type
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_generation_id`,
				`category_id`,
				`hidden_power_index`,
				`color_code`
			FROM `types`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeNotFoundException(
				"No type exists with identifier $identifier."
			);
		}

		$categoryId = $result['category_id'] !== null
			? new CategoryId($result['category_id'])
			: null;

		return new Type(
			new TypeId($result['id']),
			$identifier,
			new GenerationId($result['introduced_in_generation_id']),
			$categoryId,
			$result['hidden_power_index'],
			$result['color_code'],
		);
	}

	/**
	 * Get a type by its hidden power index.
	 *
	 * @throws TypeNotFoundException if no type exists with this hidden power
	 *     index.
	 */
	public function getByHiddenPowerIndex(int $hiddenPowerIndex) : Type
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`introduced_in_generation_id`,
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
				"No type exists with hidden power index $hiddenPowerIndex."
			);
		}

		$categoryId = $result['category_id'] !== null
			? new CategoryId($result['category_id'])
			: null;

		return new Type(
			new TypeId($result['id']),
			$result['identifier'],
			new GenerationId($result['introduced_in_generation_id']),
			$categoryId,
			$hiddenPowerIndex,
			$result['color_code'],
		);
	}
}
