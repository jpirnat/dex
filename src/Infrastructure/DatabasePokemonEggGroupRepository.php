<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\EggGroups\PokemonEggGroup;
use Jp\Dex\Domain\EggGroups\PokemonEggGroupRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

final class DatabasePokemonEggGroupRepository implements PokemonEggGroupRepositoryInterface
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
	 * Get Pokémon egg groups by Pokémon.
	 *
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonEggGroup[] Indexed by egg group id.
	 */
	public function getByPokemon(PokemonId $pokemonId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`egg_group_id`
			FROM `pokemon_egg_groups`
			WHERE `pokemon_id` = :pokemon_id'
		);
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
