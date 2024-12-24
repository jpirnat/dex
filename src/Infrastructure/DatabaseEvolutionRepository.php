<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Evolutions\Evolution;
use Jp\Dex\Domain\Evolutions\EvolutionRepositoryInterface;
use Jp\Dex\Domain\Evolutions\EvoMethodId;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionId;
use PDO;

final readonly class DatabaseEvolutionRepository implements EvolutionRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get evolutions that evolve from this form.
	 *
	 * @return Evolution[] Ordered by evo into id.
	 */
	public function getByEvoFrom(VersionGroupId $versionGroupId, FormId $evoFromId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`evo_method_id`,
				`evo_into_id`,
				`level`,
				`item_id`,
				`move_id`,
				`pokemon_id`,
				`type_id`,
				`version_id`,
				`other_parameter`
			FROM `evolutions`
			WHERE `version_group_id` = :version_group_id
				AND `evo_from_id` = :evo_from_id
			ORDER BY `evo_into_id`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':evo_from_id', $evoFromId->value, PDO::PARAM_INT);
		$stmt->execute();

		$evolutions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$evolution = new Evolution(
				$versionGroupId,
				$evoFromId,
				new EvoMethodId($result['evo_method_id']),
				new FormId($result['evo_into_id']),
				$result['level'],
				$result['item_id'] !== null ? new ItemId($result['item_id']) : null,
				$result['move_id'] !== null ? new MoveId($result['move_id']) : null,
				$result['pokemon_id'] !== null ? new PokemonId($result['pokemon_id']) : null,
				$result['type_id'] !== null ? new TypeId($result['type_id']) : null,
				$result['version_id'] !== null ? new VersionId($result['version_id']) : null,
				$result['other_parameter'],
			);

			$evolutions[] = $evolution;
		}

		return $evolutions;
	}

	/**
	 * Get evolutions that evolve into this form.
	 *
	 * @return Evolution[] Ordered by evo from id.
	 */
	public function getByEvoInto(VersionGroupId $versionGroupId, FormId $evoIntoId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`evo_from_id`,
				`evo_method_id`,
				`level`,
				`item_id`,
				`move_id`,
				`pokemon_id`,
				`type_id`,
				`version_id`,
				`other_parameter`
			FROM `evolutions`
			WHERE `version_group_id` = :version_group_id
				AND `evo_into_id` = :evo_into_id
			ORDER BY `evo_from_id`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':evo_into_id', $evoIntoId->value, PDO::PARAM_INT);
		$stmt->execute();

		$evolutions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$evolution = new Evolution(
				$versionGroupId,
				new FormId($result['evo_from_id']),
				new EvoMethodId($result['evo_method_id']),
				$evoIntoId,
				$result['level'],
				$result['item_id'] !== null ? new ItemId($result['item_id']) : null,
				$result['move_id'] !== null ? new MoveId($result['move_id']) : null,
				$result['pokemon_id'] !== null ? new PokemonId($result['pokemon_id']) : null,
				$result['type_id'] !== null ? new TypeId($result['type_id']) : null,
				$result['version_id'] !== null ? new VersionId($result['version_id']) : null,
				$result['other_parameter'],
			);

			$evolutions[] = $evolution;
		}

		return $evolutions;
	}

	/**
	 * Get evolutions triggered by this item in some way.
	 *
	 * @return Evolution[]
	 */
	public function getByItem(VersionGroupId $versionGroupId, ItemId $itemId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`evo_from_id`,
				`evo_method_id`,
				`evo_into_id`,
				`level`,
				`move_id`,
				`pokemon_id`,
				`type_id`,
				`version_id`,
				`other_parameter`
			FROM `evolutions`
			WHERE `version_group_id` = :version_group_id
				AND `item_id` = :item_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value, PDO::PARAM_INT);
		$stmt->execute();

		$evolutions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$evolution = new Evolution(
				$versionGroupId,
				new FormId($result['evo_from_id']),
				new EvoMethodId($result['evo_method_id']),
				new FormId($result['evo_into_id']),
				$result['level'],
				$itemId,
				$result['move_id'] !== null ? new MoveId($result['move_id']) : null,
				$result['pokemon_id'] !== null ? new PokemonId($result['pokemon_id']) : null,
				$result['type_id'] !== null ? new TypeId($result['type_id']) : null,
				$result['version_id'] !== null ? new VersionId($result['version_id']) : null,
				$result['other_parameter'],
			);

			$evolutions[] = $evolution;
		}

		return $evolutions;
	}

	/**
	 * Get all evolutions.
	 *
	 * @return Evolution[]
	 */
	public function getAll() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`version_group_id`,
				`evo_from_id`,
				`evo_method_id`,
				`evo_into_id`,
				`level`,
				`item_id`,
				`move_id`,
				`pokemon_id`,
				`type_id`,
				`version_id`,
				`other_parameter`
			FROM `evolutions`'
		);
		$stmt->execute();

		$evolutions = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$evolution = new Evolution(
				new VersionGroupId($result['version_group_id']),
				new FormId($result['evo_from_id']),
				new EvoMethodId($result['evo_method_id']),
				new FormId($result['evo_into_id']),
				$result['level'],
				$result['item_id'] !== null ? new ItemId($result['item_id']) : null,
				$result['move_id'] !== null ? new MoveId($result['move_id']) : null,
				$result['pokemon_id'] !== null ? new PokemonId($result['pokemon_id']) : null,
				$result['type_id'] !== null ? new TypeId($result['type_id']) : null,
				$result['version_id'] !== null ? new VersionId($result['version_id']) : null,
				$result['other_parameter'],
			);

			$evolutions[] = $evolution;
		}

		return $evolutions;
	}
}
