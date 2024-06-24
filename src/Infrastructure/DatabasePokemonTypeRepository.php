<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\PokemonType;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabasePokemonTypeRepository implements PokemonTypeRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get Pokémon's types by version group and Pokémon.
	 *
	 * @return PokemonType[] Indexed and ordered by slot.
	 */
	public function getByVgAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`slot`,
				`type_id`
			FROM `pokemon_types`
			WHERE `version_group_id` = :version_group_id
				AND `pokemon_id` = :pokemon_id
			ORDER BY `slot`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonType = new PokemonType(
				$versionGroupId,
				$pokemonId,
				$result['slot'],
				new TypeId($result['type_id']),
			);

			$pokemonTypes[$result['slot']] = $pokemonType;
		}

		return $pokemonTypes;
	}
}
