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
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseDexPokemonRepository implements DexPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
		private DexTypeRepositoryInterface $dexTypeRepository,
		private DexPokemonAbilityRepositoryInterface $dexPokemonAbilityRepository,
		private BaseStatRepositoryInterface $baseStatRepository,
	) {}

	/**
	 * Get a dex Pokémon by its id.
	 *
	 * @throws PokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : DexPokemon {
		$types = $this->dexTypeRepository->getByPokemon(
			$versionGroupId,
			$pokemonId,
			$languageId,
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemon(
			$versionGroupId,
			$pokemonId,
			$languageId,
		);
		$baseStats = $this->baseStatRepository->getByPokemon(
			$versionGroupId,
			$pokemonId,
		);

		// Normalize the base stats.
		$statIds = StatId::getByVersionGroup($versionGroupId);
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
				AND `fi`.`version_group_id` = :version_group_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
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
			$result['sort'],
		);

		return $dexPokemon;
	}

	/**
	 * Get all dex Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getWithAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : array {
		$types = $this->dexTypeRepository->getByPokemonAbility(
			$versionGroupId,
			$abilityId,
			$languageId,
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemonAbility(
			$versionGroupId,
			$abilityId,
			$languageId,
		);
		$baseStats = $this->baseStatRepository->getByPokemonAbility(
			$versionGroupId,
			$abilityId,
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
			WHERE `fi`.`version_group_id` = :version_group_id1
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				AND `p`.`id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `version_group_id` = :version_group_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
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
				$result['sort'],
			);

			$dexPokemons[] = $dexPokemon;
		}

		return $dexPokemons;
	}

	/**
	 * Get all dex Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return DexPokemon[] Indexed by Pokémon id.
	 */
	public function getWithMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array {
		$types = $this->dexTypeRepository->getByPokemonMove(
			$versionGroupId,
			$moveId,
			$languageId,
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemonMove(
			$versionGroupId,
			$moveId,
			$languageId,
		);
		$baseStats = $this->baseStatRepository->getByPokemonMove(
			$versionGroupId,
			$moveId,
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
			WHERE `fi`.`version_group_id` = :version_group_id1
				AND `p`.`id` IN (
					SELECT DISTINCT
						`f`.`pokemon_id`
					FROM `version_group_forms` AS `vgf`
					INNER JOIN `forms` AS `f`
						ON `vgf`.`form_id` = `f`.`id`
					WHERE `vgf`.`version_group_id` = :version_group_id2
				)
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				AND `p`.`id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves` AS `pm`
					WHERE `pm`.`version_group_id` <= :version_group_id3
						AND `pm`.`move_id` = :move_id
				)'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id3', $versionGroupId->value(), PDO::PARAM_INT);
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
				$result['sort'],
			);

			$dexPokemons[$result['id']] = $dexPokemon;
		}

		return $dexPokemons;
	}

	/**
	 * Get all dex Pokémon in this version group.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$types = $this->dexTypeRepository->getByPokemonVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$abilities = $this->dexPokemonAbilityRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$baseStats = $this->baseStatRepository->getByVersionGroup(
			$versionGroupId,
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
			WHERE `fi`.`version_group_id` = :version_group_id1
				AND `p`.`id` IN (
					SELECT DISTINCT
						`f`.`pokemon_id`
					FROM `version_group_forms` AS `vgf`
					INNER JOIN `forms` AS `f`
						ON `vgf`.`form_id` = `f`.`id`
					WHERE `vgf`.`version_group_id` = :version_group_id2
				)
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
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
				$result['sort'],
			);

			$dexPokemons[] = $dexPokemon;
		}

		return $dexPokemons;
	}

	/**
	 * Get all dex Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexPokemon[] Ordered by Pokémon sort value.
	 */
	public function getByType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array {
		$types = $this->dexTypeRepository->getByPokemonType(
			$versionGroupId,
			$typeId,
			$languageId,
		);
		$abilities = $this->dexPokemonAbilityRepository->getByPokemonType(
			$versionGroupId,
			$typeId,
			$languageId,
		);
		$baseStats = $this->baseStatRepository->getByPokemonType(
			$versionGroupId,
			$typeId,
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
			WHERE `fi`.`version_group_id` = :version_group_id1
				AND `p`.`id` IN (
					SELECT DISTINCT
						`f`.`pokemon_id`
					FROM `version_group_forms` AS `vgf`
					INNER JOIN `forms` AS `f`
						ON `vgf`.`form_id` = `f`.`id`
					WHERE `vgf`.`version_group_id` = :version_group_id2
				)
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
				AND `p`.`id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `version_group_id` = :version_group_id3
						AND `type_id` = :type_id
				)
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id3', $versionGroupId->value(), PDO::PARAM_INT);
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
				$result['sort'],
			);

			$dexPokemons[] = $dexPokemon;
		}

		return $dexPokemons;
	}
}
