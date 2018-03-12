<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\CategoryId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\Type;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNotFoundException;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;
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
			$hiddenPowerIndex,
			$result['color_code']
		);

		return $type;
	}

	/**
	 * Get the types of this Pokémon in this generation. Indexed by slot.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 *
	 * @return Type[]
	 */
	public function getByGenerationAndPokemon(
		Generation $generation,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pt`.`slot`,
				`t`.`id`,
				`t`.`identifier`,
				`t`.`category_id`,
				`t`.`hidden_power_index`,
				`t`.`color_code`
			FROM `pokemon_types` AS `pt`
			INNER JOIN `types` AS `t`
				ON `pt`.`type_id` = `t`.`id`
			WHERE `pt`.`generation` = :generation
				AND `pt`.`pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$types = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
				$result['hidden_power_index'],
				$result['color_code']
			);

			$types[$result['slot']] = $type;
		}

		return $types;
	}
}
