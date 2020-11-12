<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabasePokemonMoveRepository implements PokemonMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get Pokémon moves by Pokémon, in this generation and earlier. Does not
	 * include the typed Hidden Powers, or moves learned via Sketch.
	 *
	 * @param PokemonId $pokemonId
	 * @param GenerationId $generationId
	 *
	 * @return PokemonMove[] Ordered by level, then sort, for easier parsing by
	 *     DexPokemonMovesModel.
	 */
	public function getByPokemonAndGeneration(
		PokemonId $pokemonId,
		GenerationId $generationId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pm`.`version_group_id`,
				`pm`.`move_id`,
				`pm`.`move_method_id`,
				`pm`.`level`,
				`pm`.`sort`
			FROM `pokemon_moves` AS `pm`
			INNER JOIN `version_groups` AS `vg`
				ON `pm`.`version_group_id` = `vg`.`id`
			WHERE `pm`.`pokemon_id` = :pokemon_id
				AND `vg`.`generation_id` <= :generation_id
				AND `pm`.`move_id` NOT BETWEEN :hp_begin AND :hp_end
				AND `pm`.`move_method_id` <> :sketch
			ORDER BY
				`pm`.`level`,
				`pm`.`sort`'
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':hp_begin', MoveId::TYPED_HIDDEN_POWER_BEGIN, PDO::PARAM_INT);
		$stmt->bindValue(':hp_end', MoveId::TYPED_HIDDEN_POWER_END, PDO::PARAM_INT);
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
				$result['sort']
			);

			$pokemonMoves[] = $pokemonMove;
		}

		return $pokemonMoves;
	}

	/**
	 * Get Pokémon moves by move, in this generation and earlier. Does not
	 * include moves learned via Sketch.
	 *
	 * @param MoveId $moveId
	 * @param GenerationId $generationId
	 *
	 * @return PokemonMove[]
	 */
	public function getByMoveAndGeneration(
		MoveId $moveId,
		GenerationId $generationId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pm`.`pokemon_id`,
				`pm`.`version_group_id`,
				`pm`.`move_method_id`,
				`pm`.`level`,
				`pm`.`sort`
			FROM `pokemon_moves` AS `pm`
			INNER JOIN `version_groups` AS `vg`
				ON `pm`.`version_group_id` = `vg`.`id`
			WHERE `pm`.`move_id` = :move_id
				AND `vg`.`generation_id` <= :generation_id
				AND `pm`.`move_method_id` <> :sketch'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
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
				$result['sort']
			);

			$pokemonMoves[] = $pokemonMove;
		}

		return $pokemonMoves;
	}
}
