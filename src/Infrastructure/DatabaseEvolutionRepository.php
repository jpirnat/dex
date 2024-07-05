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
