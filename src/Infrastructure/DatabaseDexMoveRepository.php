<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\DexCategoryRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

final class DatabaseDexMoveRepository implements DexMoveRepositoryInterface
{
	private PDO $db;
	private DexTypeRepositoryInterface $dexTypeRepository;
	private DexCategoryRepositoryInterface $dexCategoryRepository;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 * @param DexTypeRepositoryInterface $dexTypeRepository
	 * @param DexCategoryRepositoryInterface $dexCategoryRepository
	 */
	public function __construct(
		PDO $db,
		DexTypeRepositoryInterface $dexTypeRepository,
		DexCategoryRepositoryInterface $dexCategoryRepository
	) {
		$this->db = $db;
		$this->dexTypeRepository = $dexTypeRepository;
		$this->dexCategoryRepository = $dexCategoryRepository;
	}

	/**
	 * Get all dex moves in this generation.
	 * This method is used to get data for the dex moves page.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexMove[] Ordered by name.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$dexTypes = $this->dexTypeRepository->getByGeneration(
			$generationId,
			$languageId
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`gm`.`type_id`,
				`gm`.`category_id`,
				`gm`.`pp`,
				`gm`.`power`,
				`gm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `generation_moves` AS `gm`
				ON `m`.`id` = `gm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `gm`.`generation_id` = `md`.`generation_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `gm`.`generation_id` = :generation_id1
				AND `m`.`id` IN (
					SELECT
						`vgm`.`move_id`
					FROM `version_group_moves` AS `vgm`
					INNER JOIN `version_groups` AS `vg`
						ON `vgm`.`version_group_id` = `vg`.`id`
					WHERE `vg`.`generation_id` = :generation_id2
				)
				AND `mn`.`language_id` = :language_id
			ORDER BY `mn`.`name`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexMove = new DexMove(
				$result['identifier'],
				$result['name'],
				$dexTypes[$result['type_id']],
				$dexCategories[$result['category_id']],
				$result['pp'],
				$result['power'],
				$result['accuracy'],
				(string) $result['description']
			);

			$dexMoves[] = $dexMove;
		}

		return $dexMoves;
	}

	/**
	 * Get all dex moves learned by this Pokémon.
	 * This method is used to get data for the dex Pokémon page.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexMove[] Indexed by move id.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$dexTypes = $this->dexTypeRepository->getByGeneration(
			$generationId,
			$languageId
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`id`,
				`m`.`identifier`,
				`mn`.`name`,
				`gm`.`type_id`,
				`gm`.`category_id`,
				`gm`.`pp`,
				`gm`.`power`,
				`gm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `generation_moves` AS `gm`
				ON `m`.`id` = `gm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `gm`.`generation_id` = `md`.`generation_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `gm`.`generation_id` = :generation_id1
				AND `m`.`id` IN (
					SELECT
						`vgm`.`move_id`
					FROM `version_group_moves` AS `vgm`
					INNER JOIN `version_groups` AS `vg`
						ON `vgm`.`version_group_id` = `vg`.`id`
					WHERE `vg`.`generation_id` = :generation_id2
				)
				AND `mn`.`language_id` = :language_id
				AND `m`.`id` IN (
					SELECT
						`pm`.`move_id`
					FROM `pokemon_moves` AS `pm`
					INNER JOIN `version_groups` AS `vg`
						ON `pm`.`version_group_id` = `vg`.`id`
					WHERE `pm`.`pokemon_id` = :pokemon_id
						AND `vg`.`generation_id` <= :generation_id3
				)'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id3', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexMove = new DexMove(
				$result['identifier'],
				$result['name'],
				$dexTypes[$result['type_id']],
				$dexCategories[$result['category_id']],
				$result['pp'],
				$result['power'],
				$result['accuracy'],
				(string) $result['description']
			);

			$dexMoves[$result['id']] = $dexMove;
		}

		return $dexMoves;
	}

	/**
	 * Get all dex moves of this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexMove[] Ordered by name.
	 */
	public function getByType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array {
		$dexType = $this->dexTypeRepository->getById(
			$typeId,
			$languageId
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`gm`.`type_id`,
				`gm`.`category_id`,
				`gm`.`pp`,
				`gm`.`power`,
				`gm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `generation_moves` AS `gm`
				ON `m`.`id` = `gm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `gm`.`generation_id` = `md`.`generation_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `gm`.`generation_id` = :generation_id1
				AND `m`.`id` IN (
					SELECT
						`vgm`.`move_id`
					FROM `version_group_moves` AS `vgm`
					INNER JOIN `version_groups` AS `vg`
						ON `vgm`.`version_group_id` = `vg`.`id`
					WHERE `vg`.`generation_id` = :generation_id2
				)
				AND `mn`.`language_id` = :language_id
				AND `gm`.`type_id` = :type_id
			ORDER BY `mn`.`name`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexMove = new DexMove(
				$result['identifier'],
				$result['name'],
				$dexType,
				$dexCategories[$result['category_id']],
				$result['pp'],
				$result['power'],
				$result['accuracy'],
				(string) $result['description']
			);

			$dexMoves[] = $dexMove;
		}

		return $dexMoves;
	}
}
