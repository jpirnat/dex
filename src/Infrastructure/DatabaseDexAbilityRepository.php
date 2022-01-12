<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseDexAbilityRepository implements DexAbilityRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the dex abilities available in this generation. This method is used
	 * to get data for the dex abilities page.
	 *
	 * @return array Ordered by ability name.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		// Get Pokémon grouped by ability for this generation.
		$stmt = $this->db->prepare(
			'SELECT
				`pa`.`ability_id`,
				`p`.`identifier`,
				`fi`.`image` AS `icon`,
				`pn`.`name`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `pokemon` AS `p`
				ON `pa`.`pokemon_id` = `p`.`id`
			INNER JOIN `form_icons` AS `fi`
				ON `pa`.`generation_id` = `fi`.`generation_id`
				AND `pa`.`pokemon_id` = `fi`.`form_id`
			INNER JOIN `pokemon_names` AS `pn`
				ON `pa`.`pokemon_id` = `pn`.`pokemon_id`
			WHERE `pa`.`generation_id` = :generation_id
				AND `fi`.`is_female` = 0
				AND `fi`.`is_right` = 0
				AND `pn`.`language_id` = :language_id
			ORDER BY `p`.`sort`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$abilityPokemon = $stmt->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);

		$stmt = $this->db->prepare(
			'SELECT
				`a`.`id`,
				`a`.`identifier`,
				`an`.`name`,
				`ad`.`description`
			FROM `abilities` AS `a`
			INNER JOIN `ability_names` AS `an`
				ON `a`.`id` = `an`.`ability_id`
			LEFT JOIN `ability_descriptions` AS `ad`
				ON `ad`.`generation_id` = :generation_id1
				AND `an`.`language_id` = `ad`.`language_id`
				AND `an`.`ability_id` = `ad`.`ability_id`
			WHERE `a`.`id` IN (
				SELECT
					`ability_id`
				FROM `pokemon_abilities`
				WHERE `generation_id` = :generation_id2
			)
			AND `an`.`language_id` = :language_id
			ORDER BY `name`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$abilities[] = [
				'identifier' => $result['identifier'],
				'name' => $result['name'],
				'description' => $result['description'] ?? '-',
				'pokemon' => $abilityPokemon[$result['id']] ?? [],
			];
		}

		return $abilities;
	}

	/**
	 * Get the dex abilities of this Pokémon. This method is used to get data
	 * for the dex Pokémon page.
	 *
	 * @return array Ordered by slot.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`a`.`id`,
				`a`.`identifier`,
				`an`.`name`,
				`ad`.`description`,
				`pa`.`is_hidden_ability`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `abilities` AS `a`
				ON `pa`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `a`.`id` = `an`.`ability_id`
			LEFT JOIN `ability_descriptions` AS `ad`
				ON `pa`.`generation_id` = `ad`.`generation_id`
				AND `an`.`language_id` = `ad`.`language_id`
				AND `an`.`ability_id` = `ad`.`ability_id`
			WHERE `pa`.`generation_id` = :generation_id
				AND `pa`.`pokemon_id` = :pokemon_id
				AND `an`.`language_id` = :language_id
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ability = [
				'id' => $result['id'],
				'identifier' => $result['identifier'],
				'name' => $result['name'],
				'description' => $result['description'] ?? '-',
				'isHiddenAbility' => (bool) $result['is_hidden_ability'],
			];

			$abilities[] = $ability;
		}

		return $abilities;
	}
}
