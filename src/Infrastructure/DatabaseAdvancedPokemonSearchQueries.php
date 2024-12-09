<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Application\Models\AdvancedPokemonSearch\AdvancedPokemonSearchQueriesInterface;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseAdvancedPokemonSearchQueries implements AdvancedPokemonSearchQueriesInterface
{
	public function __construct(
		private DatabaseDexPokemonRepository $dexPokemonRepository,
		private PDO $db,
	) {}

	/**
	 * Get all egg group ids, indexed by identifier.
	 *
	 * @return EggGroupId[] Indexed by identifier.
	 */
	public function getEggGroupIdentifiersToIds() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`id`
			FROM `egg_groups`'
		);
		$stmt->execute();

		$eggGroupIds = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$eggGroupIds[$result['identifier']] = new EggGroupId($result['id']);
		}

		return $eggGroupIds;
	}

	/**
	 * Get all move ids, indexed by identifier.
	 *
	 * @return MoveId[] Indexed by identifier.
	 */
	public function getMoveIdentifiersToIds() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`id`
			FROM `moves`'
		);
		$stmt->execute();

		$moveIds = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$moveIds[$result['identifier']] = new MoveId($result['id']);
		}

		return $moveIds;
	}

	/**
	 * Get dex Pokémon for this advanced search.
	 *
	 * @param EggGroupId[] $eggGroupIds
	 * @param GenderRatio[] $genderRatios
	 * @param MoveId[] $moveIds
	 *
	 * @return DexPokemon[] Indexed by Pokémon id. Ordered by Pokémon sort value.
	 */
	public function search(
		VersionGroupId $versionGroupId,
		?AbilityId $abilityId,
		array $eggGroupIds,
		string $eggGroupsOperator,
		array $genderRatios,
		string $genderRatiosOperator,
		array $moveIds,
		bool $includeTransferMoves,
		LanguageId $languageId,
	) : array {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();

		$whereClauses = [];
		$whereClauses[] = "`vp`.`version_group_id` = $versionGroupId";

		if ($abilityId) {
			$abilityId = $abilityId->value();
			$whereClauses[] = "$abilityId IN (`vp`.`ability1_id`, `vp`.`ability2_id`, `vp`.`ability3_id`)";
		}

		if ($eggGroupIds) {
			$eggGroupClauses = [];
			foreach ($eggGroupIds as $eggGroupId) {
				$eggGroupId = $eggGroupId->value();
				$eggGroupClauses[] = "$eggGroupId IN (`vp`.`egg_group1_id`, `egg_group2_id`)";
			}

			$andOr = match ($eggGroupsOperator) {
				'and' => 'AND',
				'or' => 'OR',
				default => 'OR',
			};
			$eggGroupClauses = implode(" $andOr ", $eggGroupClauses);
			$whereClauses[] = "($eggGroupClauses)";
		}

		if ($genderRatios) {
			$genderRatios = array_map(
				function (GenderRatio $genderRatio) : int {
					return $genderRatio->value();
				},
				$genderRatios,
			);
			$genderRatios = implode(', ', $genderRatios);
			$inOrNotIn = match ($genderRatiosOperator) {
				'any' => 'IN',
				'none' => 'NOT IN',
				default => 'IN',
			};
			$whereClauses[] = "`p`.`gender_ratio` $inOrNotIn ($genderRatios)";
		}

		if ($moveIds) {
			foreach ($moveIds as $moveId) {
				$moveId = $moveId->value();
				if ($includeTransferMoves) {
					$whereClauses[] = "`vp`.`pokemon_id` IN (
		SELECT
			`pokemon_id`
		FROM `pokemon_moves`
		WHERE `version_group_id` IN (
			SELECT
				`from_vg_id`
			FROM `vg_move_transfers`
			WHERE `into_vg_id` = $versionGroupId
		)
		AND `move_id` = $moveId
	)";
				} else {
					$whereClauses[] = "`vp`.`pokemon_id` IN (
		SELECT
			`pokemon_id`
		FROM `pokemon_moves`
		WHERE `version_group_id` = $versionGroupId
			AND `move_id` = $moveId
	)";
				}
			}
		}

		$whereClauses[] = "`pn`.`language_id` = $languageId";

		$whereClauses = implode("\n\tAND ", $whereClauses);

		$baseQuery = $this->dexPokemonRepository->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE $whereClauses
ORDER BY `p`.`sort`"
		);
		return $this->dexPokemonRepository->executeAndFetch($stmt);
	}
}
