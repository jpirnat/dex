<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\EggGroups\PokemonEggGroup;
use Jp\Dex\Domain\EggGroups\PokemonEggGroupRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabasePokemonEggGroupRepository implements PokemonEggGroupRepositoryInterface
{
	private PDO $db;

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
	 * Get Pokémon egg groups by Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonEggGroup[] Indexed by egg group id.
	 */
	public function getByPokemon(GenerationId $generationId, PokemonId $pokemonId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`egg_group_id`
			FROM `pokemon_egg_groups`
			WHERE `generation_id` = :generation_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonEggGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonEggGroup = new PokemonEggGroup(
				$pokemonId,
				new EggGroupId($result['egg_group_id'])
			);

			$pokemonEggGroups[$result['egg_group_id']] = $pokemonEggGroup;
		}

		return $pokemonEggGroups;
	}
}
