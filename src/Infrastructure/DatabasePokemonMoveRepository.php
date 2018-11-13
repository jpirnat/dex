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

class DatabasePokemonMoveRepository implements PokemonMoveRepositoryInterface
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
	 * Get Pokémon moves by Pokémon, in this generation and earlier. Does not
	 * include moves learned via Sketch.
	 *
	 * @param PokemonId $pokemonId
	 * @param GenerationId $generationId
	 *
	 * @return PokemonMove[]
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
				AND `pm`.`move_method_id` <> :sketch'
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
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
}
