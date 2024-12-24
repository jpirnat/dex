<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\VgPokemon;
use Jp\Dex\Domain\Pokemon\VgPokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseVgPokemonRepository implements VgPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a version group Pokémon by version group and Pokémon.
	 *
	 * @throws VgPokemonNotFoundException if no version group Pokémon
	 *     exists for this version group and Pokémon.
	 */
	public function getByVgAndPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	): VgPokemon {
		$stmt = $this->db->prepare(
			'SELECT
				`type1_id`,
				`type2_id`,
				`ability1_id`,
				`ability2_id`,
				`ability3_id`,
				`base_hp`,
				`base_atk`,
				`base_def`,
				`base_spa`,
				`base_spd`,
				`base_spe`,
				`base_spc`,
				`egg_group1_id`,
				`egg_group2_id`,
				`base_experience`,
				`ev_hp`,
				`ev_atk`,
				`ev_def`,
				`ev_spa`,
				`ev_spd`,
				`ev_spe`
			FROM `vg_pokemon`
			WHERE `version_group_id` = :version_group_id
				AND `pokemon_id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VgPokemonNotFoundException(
				"No version group Pokémon exists for version group id $versionGroupId->value and Pokémon id $pokemonId->value."
			);
		}

		return new VgPokemon(
			$versionGroupId,
			$pokemonId,
			new TypeId($result['type1_id']),
			$result['type2_id'] !== null ? new TypeId($result['type2_id']) : null,
			$result['ability1_id'] !== null ? new AbilityId($result['ability1_id']) : null,
			$result['ability2_id'] !== null ? new AbilityId($result['ability2_id']) : null,
			$result['ability3_id'] !== null ? new AbilityId($result['ability3_id']) : null,
			$result['base_hp'],
			$result['base_atk'],
			$result['base_def'],
			$result['base_spa'],
			$result['base_spd'],
			$result['base_spe'],
			$result['base_spc'],
			$result['egg_group1_id'] !== null ? new EggGroupId($result['egg_group1_id']) : null,
			$result['egg_group2_id'] !== null ? new EggGroupId($result['egg_group2_id']) : null,
			$result['base_experience'],
			$result['ev_hp'],
			$result['ev_atk'],
			$result['ev_def'],
			$result['ev_spa'],
			$result['ev_spd'],
			$result['ev_spe'],
		);
	}
}
