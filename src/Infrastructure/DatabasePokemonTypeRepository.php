<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\PokemonType;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabasePokemonTypeRepository implements PokemonTypeRepositoryInterface
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
	 * Get Pokémon's types by generation and Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonType[] Ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`slot`,
				`type_id`
			FROM `pokemon_types`
			WHERE `generation_id` = :generation_id
				AND `pokemon_id` = :pokemon_id
			ORDER BY `slot` ASC'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonType = new PokemonType(
				$generationId,
				$pokemonId,
				$result['slot'],
				new TypeId($result['type_id'])
			);

			$pokemonTypes[$result['slot']] = $pokemonType;
		}

		return $pokemonTypes;
	}
}
