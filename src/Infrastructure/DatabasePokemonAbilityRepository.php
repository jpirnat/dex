<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\PokemonAbility;
use Jp\Dex\Domain\Abilities\PokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabasePokemonAbilityRepository implements PokemonAbilityRepositoryInterface
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
	 * Get a Pokémon's abilities by generation and Pokémon.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonAbility[] Ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		Generation $generation,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`slot`,
				`ability_id`,
				`is_hidden_ability`
			FROM `pokemon_abilities`
			WHERE `generation` = :generation
				AND `pokemon_id` = :pokemon_id
			ORDER BY `slot` ASC'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonAbility = new PokemonAbility(
				$generation,
				$pokemonId,
				$result['slot'],
				new AbilityId($result['ability_id']),
				(bool) $result['is_hidden_ability']
			);

			$pokemonAbilities[] = $pokemonAbility;
		}

		return $pokemonAbilities;
	}

	/**
	 * Get Pokémon abilities by generation and ability.
	 *
	 * @param Generation $generation
	 * @param AbilityId $abilityId
	 *
	 * @return PokemonAbility[]
	 */
	public function getByGenerationAndAbility(
		Generation $generation,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`slot`,
				`is_hidden_ability`
			FROM `pokemon_abilities`
			WHERE `generation` = :generation
				AND `ability_id` = :ability_id'
		);
		$stmt->bindValue(':generation', $generation->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonAbility = new PokemonAbility(
				$generation,
				new PokemonId($result['pokemon_id']),
				$result['slot'],
				$abilityId,
				(bool) $result['is_hidden_ability']
			);

			$pokemonAbilities[] = $pokemonAbility;
		}

		return $pokemonAbilities;
	}
}
