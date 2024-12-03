<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Application\Models\AdvancedMoveSearch\AdvancedMoveSearchQueriesInterface;
use Jp\Dex\Domain\Categories\CategoryId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\Flags\MoveFlagId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseAdvancedMoveSearchQueries implements AdvancedMoveSearchQueriesInterface
{
	public function __construct(
		private DatabaseDexMoveRepository $moveRepository,
		private PDO $db,
	) {}

	/**
	 * Get dex moves for this advanced search.
	 *
	 * @param TypeId[] $typeIds
	 * @param CategoryId[] $categoryIds
	 * @param MoveFlagId[] $yesFlagIds
	 * @param MoveFlagId[] $noFlagIds
	 *
	 * @return DexMove[] Indexed by move id. Ordered by name.
	 */
	public function search(
		VersionGroupId $versionGroupId,
		array $typeIds,
		array $categoryIds,
		array $yesFlagIds,
		array $noFlagIds,
		?PokemonId $pokemonId,
		bool $includeTransferMoves,
		LanguageId $languageId,
	) : array {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();

		$whereClauses = [];
		$whereClauses[] = "`vm`.`version_group_id` = $versionGroupId";

		if ($typeIds) {
			$typeIds = array_map(
				function (TypeId $typeId) : int {
					return $typeId->value();
				},
				$typeIds,
			);
			$typeIds = implode(', ', $typeIds);
			$whereClauses[] = "`vm`.`type_id` IN ($typeIds)";
		}

		if ($categoryIds) {
			$categoryIds = array_map(
				function (CategoryId $categoryId) : int {
					return $categoryId->value();
				},
				$categoryIds,
			);
			$categoryIds = implode(', ', $categoryIds);
			$whereClauses[] = "`vm`.`category_id` IN ($categoryIds)";
		}

		if ($yesFlagIds) {
			foreach ($yesFlagIds as $yesFlagId) {
				$yesFlagId = $yesFlagId->value();
				$whereClauses[] = "`vm`.`move_id` IN (
		SELECT
			`move_id`
		FROM `vg_moves_flags`
		WHERE `version_group_id` = $versionGroupId
			AND `flag_id` = $yesFlagId
	)";
			}
		}

		if ($noFlagIds) {
			foreach ($noFlagIds as $noFlagId) {
				$noFlagId = $noFlagId->value();
				$whereClauses[] = "`vm`.`move_id` NOT IN (
		SELECT
			`move_id`
		FROM `vg_moves_flags`
		WHERE `version_group_id` = $versionGroupId
			AND `flag_id` = $noFlagId
	)";
			}
		}

		if ($pokemonId) {
			$pokemonId = $pokemonId->value();
			if ($includeTransferMoves) {
				$whereClauses[] = "`vm`.`move_id` IN (
		SELECT
			`move_id`
		FROM `pokemon_moves`
		WHERE `version_group_id` IN (
			SELECT
				`from_vg_id`
			FROM `vg_move_transfers`
			WHERE `into_vg_id` = $versionGroupId
		)
		AND `pokemon_id` = $pokemonId
	)";
			} else {
				$whereClauses[] = "`vm`.`move_id` IN (
		SELECT
			`move_id`
		FROM `pokemon_moves`
		WHERE `version_group_id` = $versionGroupId
			AND `pokemon_id` = $pokemonId
	)";
			}
		}

		$whereClauses[] = "`vm`.`can_use_move` = 1";
		$whereClauses[] = "`mn`.`language_id` = $languageId";

		$whereClauses = implode("\n\tAND ", $whereClauses);

		$baseQuery = $this->moveRepository->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
$whereClauses
ORDER BY `name`"
		);
		return $this->moveRepository->executeAndFetch($stmt);
	}
}
