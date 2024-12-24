<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabasePokemonMoveRepository implements PokemonMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get Pokémon moves available for this Pokémon in this version group, based
	 * on all the version groups that can transfer movesets into this one.
	 *
	 * @return PokemonMove[] Ordered by level, then sort, for easier parsing by
	 *     DexPokemonMovesModel.
	 */
	public function getByIntoVgAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`version_group_id`,
				`move_id`,
				`move_method_id`,
				`level`,
				`sort`
			FROM `pokemon_moves` AS `pm`
			WHERE `version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = :version_group_id
			)
			AND `pokemon_id` = :pokemon_id
			AND `move_method_id` <> :sketch
			ORDER BY
				`level`,
				`sort`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':sketch', MoveMethodId::SKETCH, PDO::PARAM_INT);
		$stmt->execute();

		$pokemonMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonMove = new PokemonMove(
				$pokemonId,
				new VersionGroupId($result['version_group_id']),
				new MoveId($result['move_id']),
				new MoveMethodId($result['move_method_id']),
				$result['level'],
				$result['sort'],
			);

			$pokemonMoves[] = $pokemonMove;
		}

		return $pokemonMoves;
	}

	/**
	 * Get Pokémon moves available for this move in this version group,
	 * based on all the version groups that can transfer movesets into this one.
	 * Does not include moves learned via Sketch.
	 *
	 * @return PokemonMove[]
	 */
	public function getByIntoVgAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`version_group_id`,
				`move_method_id`,
				`level`,
				`sort`
			FROM `pokemon_moves`
			WHERE `version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = :version_group_id
			)
			AND `move_id` = :move_id
			AND `move_method_id` <> :sketch'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value, PDO::PARAM_INT);
		$stmt->bindValue(':sketch', MoveMethodId::SKETCH, PDO::PARAM_INT);
		$stmt->execute();

		$pokemonMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonMove = new PokemonMove(
				new PokemonId($result['pokemon_id']),
				new VersionGroupId($result['version_group_id']),
				$moveId,
				new MoveMethodId($result['move_method_id']),
				$result['level'],
				$result['sort'],
			);

			$pokemonMoves[] = $pokemonMove;
		}

		return $pokemonMoves;
	}
}
