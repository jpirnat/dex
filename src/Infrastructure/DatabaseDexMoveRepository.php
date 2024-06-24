<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Categories\DexCategoryRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseDexMoveRepository implements DexMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
		private DexTypeRepositoryInterface $dexTypeRepository,
		private DexCategoryRepositoryInterface $dexCategoryRepository,
	) {}

	/**
	 * Get a dex move by its id.
	 * This method is used to get data for the dex move page.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : DexMove {
		// TODO: This can be optimized.
		$dexTypes = $this->dexTypeRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`vgm`.`type_id`,
				`vgm`.`category_id`,
				`vgm`.`pp`,
				`vgm`.`power`,
				`vgm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `vg_moves` AS `vgm`
				ON `m`.`id` = `vgm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `vgm`.`version_group_id` = `md`.`version_group_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `vgm`.`version_group_id` = :version_group_id
				AND `m`.`id` = :move_id
				AND `mn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return new DexMove(
			$result['identifier'],
			$result['name'],
			$dexTypes[$result['type_id']],
			$dexCategories[$result['category_id']],
			$result['pp'],
			$result['power'],
			$result['accuracy'],
			(string) $result['description'],
		);
	}

	/**
	 * Get all dex moves in this version group.
	 * This method is used to get data for the dex moves page.
	 *
	 * @return DexMove[] Ordered by name.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$dexTypes = $this->dexTypeRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`vgm`.`type_id`,
				`vgm`.`category_id`,
				`vgm`.`pp`,
				`vgm`.`power`,
				`vgm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `vg_moves` AS `vgm`
				ON `m`.`id` = `vgm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `vgm`.`version_group_id` = `md`.`version_group_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `vgm`.`version_group_id` = :version_group_id
				AND `vgm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id
			ORDER BY `mn`.`name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
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
				(string) $result['description'],
			);

			$dexMoves[] = $dexMove;
		}

		return $dexMoves;
	}

	/**
	 * Get all dex moves learned by this Pokémon.
	 * This method is used to get data for the dex Pokémon page.
	 *
	 * @return DexMove[] Indexed by move id.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$dexTypes = $this->dexTypeRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`id`,
				`m`.`identifier`,
				`mn`.`name`,
				`vgm`.`type_id`,
				`vgm`.`category_id`,
				`vgm`.`pp`,
				`vgm`.`power`,
				`vgm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `vg_moves` AS `vgm`
				ON `m`.`id` = `vgm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `vgm`.`version_group_id` = `md`.`version_group_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `vgm`.`version_group_id` = :version_group_id1
				AND `vgm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id
				AND `m`.`id` IN (
					SELECT
						`move_id`
					FROM `pokemon_moves`
					WHERE `pokemon_id` = :pokemon_id
						AND `version_group_id` <= :version_group_id2
				)'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
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
				(string) $result['description'],
			);

			$dexMoves[$result['id']] = $dexMove;
		}

		return $dexMoves;
	}

	/**
	 * Get all dex moves of this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexMove[] Ordered by name.
	 */
	public function getByType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array {
		$dexType = $this->dexTypeRepository->getById(
			$typeId,
			$languageId,
		);
		$dexCategories = $this->dexCategoryRepository->getByLanguage($languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`m`.`identifier`,
				`mn`.`name`,
				`vgm`.`type_id`,
				`vgm`.`category_id`,
				`vgm`.`pp`,
				`vgm`.`power`,
				`vgm`.`accuracy`,
				`md`.`description`
			FROM `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			INNER JOIN `vg_moves` AS `vgm`
				ON `m`.`id` = `vgm`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `vgm`.`version_group_id` = `md`.`version_group_id`
				AND `mn`.`language_id` = `md`.`language_id`
				AND `m`.`id` = `md`.`move_id`
			WHERE `vgm`.`version_group_id` = :version_group_id
				AND `vgm`.`can_use_move` = 1
				AND `mn`.`language_id` = :language_id
				AND `vgm`.`type_id` = :type_id
			ORDER BY `mn`.`name`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
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
				(string) $result['description'],
			);

			$dexMoves[] = $dexMove;
		}

		return $dexMoves;
	}
}
