<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Pokemon\ExperienceGroupId;
use Jp\Dex\Domain\Pokemon\Pokemon;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Species\SpeciesId;
use PDO;

final readonly class DatabasePokemonRepository implements PokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a Pokémon by its id.
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(PokemonId $pokemonId) : Pokemon
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`pokemon_identifier`,
				`species_id`,
				`is_default_pokemon`,
				`experience_group_id`,
				`gender_ratio`,
				`smogon_dex_identifier`,
				`sort`
			FROM `pokemon`
			WHERE `id` = :pokemon_id
			LIMIT 1'
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new PokemonNotFoundException(
				'No Pokémon exists with id ' . $pokemonId->value() . '.'
			);
		}

		$pokemon = new Pokemon(
			$pokemonId,
			$result['identifier'],
			$result['pokemon_identifier'],
			new SpeciesId($result['species_id']),
			(bool) $result['is_default_pokemon'],
			new ExperienceGroupId($result['experience_group_id']),
			$result['gender_ratio'],
			$result['smogon_dex_identifier'],
			$result['sort'],
		);

		return $pokemon;
	}

	/**
	 * Get a Pokémon by its identifier.
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Pokemon
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`pokemon_identifier`,
				`species_id`,
				`is_default_pokemon`,
				`experience_group_id`,
				`gender_ratio`,
				`smogon_dex_identifier`,
				`sort`
			FROM `pokemon`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new PokemonNotFoundException(
				"No Pokémon exists with identifier $identifier."
			);
		}

		$pokemon = new Pokemon(
			new PokemonId($result['id']),
			$identifier,
			$result['pokemon_identifier'],
			new SpeciesId($result['species_id']),
			(bool) $result['is_default_pokemon'],
			new ExperienceGroupId($result['experience_group_id']),
			$result['gender_ratio'],
			$result['smogon_dex_identifier'],
			$result['sort'],
		);

		return $pokemon;
	}

	/**
	 * Get all Pokémon.
	 *
	 * @return Pokemon[] Indexed by id. Ordered by sort.
	 */
	public function getAll() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`pokemon_identifier`,
				`species_id`,
				`is_default_pokemon`,
				`experience_group_id`,
				`gender_ratio`,
				`smogon_dex_identifier`,
				`sort`
			FROM `pokemon`
			ORDER BY `sort`'
		);
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemon = new Pokemon(
				new PokemonId($result['id']),
				$result['identifier'],
				$result['pokemon_identifier'],
				new SpeciesId($result['species_id']),
				(bool) $result['is_default_pokemon'],
				new ExperienceGroupId($result['experience_group_id']),
				$result['gender_ratio'],
				$result['smogon_dex_identifier'],
				$result['sort'],
			);

			$pokemons[$result['id']] = $pokemon;
		}

		return $pokemons;
	}
}
