<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\DexPokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseDexPokemonRepository implements DexPokemonRepositoryInterface
{
	private PDO $db;
	private DexTypeRepositoryInterface $dexTypeRepository;
	private DexPokemonAbilityRepositoryInterface $dexPokemonAbilityRepository;
	private BaseStatRepositoryInterface $baseStatRepository;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 * @param DexTypeRepositoryInterface $dexTypeRepository
	 * @param DexPokemonAbilityRepositoryInterface $dexPokemonAbilityRepository
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 */
	public function __construct(
		PDO $db,
		DexTypeRepositoryInterface $dexTypeRepository,
		DexPokemonAbilityRepositoryInterface $dexPokemonAbilityRepository,
		BaseStatRepositoryInterface $baseStatRepository
	) {
		$this->db = $db;
		$this->dexTypeRepository = $dexTypeRepository;
		$this->dexPokemonAbilityRepository = $dexPokemonAbilityRepository;
		$this->baseStatRepository = $baseStatRepository;
	}

	/**
	 * Get a dex Pokémon by its id.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this id.
	 *
	 * @return DexPokemon.
	 */
	public function getById(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : DexPokemon {
		$types = $this->dexTypeRepository->getByPokemon(
			$generationId,
			$pokemonId,
			$languageId
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemon(
			$generationId,
			$pokemonId,
			$languageId
		);
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);

		// Normalize the base stats.
		$statIds = StatId::getByGeneration($generationId);
		$idsToIdentifiers = StatId::getIdsToIdentifiers();
		$normalized = [];
		foreach ($statIds as $statId) {
			$identifier = $idsToIdentifiers[$statId->value()];
			$normalized[$identifier] = $baseStats->get($statId)->getValue();
		}
		$baseStats = $normalized;

		$stmt = $this->db->prepare(
			'SELECT
				`p`.`id`,
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`p`.`sort`
			FROM `pokemon` AS `p`
			INNER JOIN `form_icons` AS `fi`
				ON `p`.`id` = `fi`.`form_id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `p`.`id` = :pokemon_id
				AND `fi`.`generation_id` = :generation_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new PokemonNotFoundException(
				'No Pokémon exists with id ' . $pokemonId->value() . '.'
			);
		}

		$dexPokemon = new DexPokemon(
			$result['icon'],
			$result['identifier'],
			$result['name'],
			$types ?? [],
			$abilities ?? [],
			$baseStats,
			(int) array_sum($baseStats),
			$result['sort']
		);

		return $dexPokemon;
	}

	/**
	 * Get all dex Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getWithAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : array {
		$types = $this->dexTypeRepository->getByPokemonAbility(
			$generationId,
			$abilityId,
			$languageId
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemonAbility(
			$generationId,
			$abilityId,
			$languageId
		);
		$baseStats = $this->baseStatRepository->getByPokemonAbility(
			$generationId,
			$abilityId
		);

		$stmt = $this->db->prepare(
			'SELECT
				`p`.`id`,
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`p`.`sort`
			FROM `pokemon` AS `p`
			INNER JOIN `form_icons` AS `fi`
				ON `p`.`id` = `fi`.`form_id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `fi`.`generation_id` = :generation_id1
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				AND `p`.`id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `generation_id` = :generation_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonBaseStats = $baseStats[$result['id']] ?? [];

			$dexPokemon = new DexPokemon(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				$types[$result['id']] ?? [],
				$abilities[$result['id']] ?? [],
				$pokemonBaseStats,
				(int) array_sum($pokemonBaseStats),
				$result['sort']
			);

			$dexPokemons[] = $dexPokemon;
		}

		return $dexPokemons;
	}

	/**
	 * Get all dex Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon[] Indexed by Pokémon id.
	 */
	public function getWithMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array {
		$types = $this->dexTypeRepository->getByPokemonMove(
			$generationId,
			$moveId,
			$languageId
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemonMove(
			$generationId,
			$moveId,
			$languageId
		);
		$baseStats = $this->baseStatRepository->getByPokemonMove(
			$generationId,
			$moveId
		);

		$stmt = $this->db->prepare(
			'SELECT
				`p`.`id`,
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`p`.`sort`
			FROM `pokemon` AS `p`
			INNER JOIN `form_icons` AS `fi`
				ON `p`.`id` = `fi`.`form_id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `fi`.`generation_id` = :generation_id1
				AND `p`.`id` IN (
					SELECT
						`vgp`.`pokemon_id`
					FROM `version_group_pokemon` AS `vgp`
					INNER JOIN `version_groups` AS `vg`
						ON `vgp`.`version_group_id` = `vg`.`id`
					WHERE `vg`.`generation_id` = :generation_id2
				)
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				AND `p`.`id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves` AS `pm`
					INNER JOIN `version_groups` AS `vg`
						ON `pm`.`version_group_id` = `vg`.`id`
					WHERE `pm`.`move_id` = :move_id
						AND `vg`.`generation_id` <= :generation_id3
				)'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id3', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonBaseStats = $baseStats[$result['id']] ?? [];

			$dexPokemon = new DexPokemon(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				$types[$result['id']] ?? [],
				$abilities[$result['id']] ?? [],
				$pokemonBaseStats,
				(int) array_sum($pokemonBaseStats),
				$result['sort']
			);

			$dexPokemons[$result['id']] = $dexPokemon;
		}

		return $dexPokemons;
	}

	/**
	 * Get all dex Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$types = $this->dexTypeRepository->getByPokemonGeneration(
			$generationId,
			$languageId
		);
		$abilities = $this->dexPokemonAbilityRepository->getByGeneration(
			$generationId,
			$languageId
		);
		$baseStats = $this->baseStatRepository->getByGeneration(
			$generationId
		);

		$stmt = $this->db->prepare(
			'SELECT
				`p`.`id`,
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`p`.`sort`
			FROM `pokemon` AS `p`
			INNER JOIN `form_icons` AS `fi`
				ON `p`.`id` = `fi`.`form_id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `fi`.`generation_id` = :generation_id1
				AND `p`.`id` IN (
					SELECT
						`vgp`.`pokemon_id`
					FROM `version_group_pokemon` AS `vgp`
					INNER JOIN `version_groups` AS `vg`
						ON `vgp`.`version_group_id` = `vg`.`id`
					WHERE `vg`.`generation_id` = :generation_id2
				)
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonBaseStats = $baseStats[$result['id']] ?? [];

			$dexPokemon = new DexPokemon(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				$types[$result['id']] ?? [],
				$abilities[$result['id']] ?? [],
				$pokemonBaseStats,
				(int) array_sum($pokemonBaseStats),
				$result['sort']
			);

			$dexPokemons[] = $dexPokemon;
		}

		return $dexPokemons;
	}

	/**
	 * Get all dex Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getByType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array {
		$types = $this->dexTypeRepository->getByPokemonType(
			$generationId,
			$typeId,
			$languageId
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemonType(
			$generationId,
			$typeId,
			$languageId
		);
		$baseStats = $this->baseStatRepository->getByPokemonType(
			$generationId,
			$typeId
		);

		$stmt = $this->db->prepare(
			'SELECT
				`p`.`id`,
				`fi`.`image` AS `icon`,
				`p`.`identifier`,
				`pn`.`name`,
				`p`.`sort`
			FROM `pokemon` AS `p`
			INNER JOIN `form_icons` AS `fi`
				ON `p`.`id` = `fi`.`form_id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `fi`.`generation_id` = :generation_id1
				AND `p`.`id` IN (
					SELECT
						`vgp`.`pokemon_id`
					FROM `version_group_pokemon` AS `vgp`
					INNER JOIN `version_groups` AS `vg`
						ON `vgp`.`version_group_id` = `vg`.`id`
					WHERE `vg`.`generation_id` = :generation_id2
				)
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				AND `p`.`id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `generation_id` = :generation_id3
						AND `type_id` = :type_id
				)
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id3', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonBaseStats = $baseStats[$result['id']] ?? [];

			$dexPokemon = new DexPokemon(
				$result['icon'],
				$result['identifier'],
				$result['name'],
				$types[$result['id']] ?? [],
				$abilities[$result['id']] ?? [],
				$pokemonBaseStats,
				(int) array_sum($pokemonBaseStats),
				$result['sort']
			);

			$dexPokemons[] = $dexPokemon;
		}

		return $dexPokemons;
	}
}
