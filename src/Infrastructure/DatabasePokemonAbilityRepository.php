<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\PokemonAbility;
use Jp\Dex\Domain\Abilities\PokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;
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
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return PokemonAbility[] Ordered by slot.
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`slot`,
				`ability_id`,
				`is_hidden_ability`
			FROM `pokemon_abilities`
			WHERE `generation_id` = :generation_id
				AND `pokemon_id` = :pokemon_id
			ORDER BY `slot`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonAbility = new PokemonAbility(
				$generationId,
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
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 *
	 * @return PokemonAbility[]
	 */
	public function getByGenerationAndAbility(
		GenerationId $generationId,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`slot`,
				`is_hidden_ability`
			FROM `pokemon_abilities`
			WHERE `generation_id` = :generation_id
				AND `ability_id` = :ability_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonAbility = new PokemonAbility(
				$generationId,
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
