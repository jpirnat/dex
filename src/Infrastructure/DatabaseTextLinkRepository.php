<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\TextLinks\TextLinkItem;
use Jp\Dex\Domain\TextLinks\TextLinkMove;
use Jp\Dex\Domain\TextLinks\TextLinkNotFoundException;
use Jp\Dex\Domain\TextLinks\TextLinkPokemon;
use Jp\Dex\Domain\TextLinks\TextLinkRepositoryInterface;
use Jp\Dex\Domain\TextLinks\TextLinkType;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseTextLinkRepository implements TextLinkRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a text link for this item.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForItem(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		ItemId $itemId,
	) : TextLinkItem {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();
		$itemId = $itemId->value();

		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`identifier` AS `vg_identifier`,
				`i`.`identifier` AS `item_identifier`,
				COALESCE(`id`.`name`, `in`.`name`) AS `item_name`
			FROM `version_groups` AS `vg`
			INNER JOIN `items` AS `i`
			INNER JOIN `item_names` AS `in`
				ON `i`.`id` = `in`.`item_id`
			LEFT JOIN `item_descriptions` AS `id`
				ON `id`.`version_group_id` = `vg`.`id`
				AND `id`.`language_id` = `in`.`language_id`
				AND `id`.`item_id` = `i`.`id`
			WHERE `vg`.`id` = :version_group_id
				AND `i`.`id` = :item_id
				AND `in`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TextLinkNotFoundException(
				"No text link can be made with version group id $versionGroupId"
				. ", language id $languageId, and item id $itemId."
			);
		}

		return new TextLinkItem(
			$result['vg_identifier'],
			$result['item_identifier'],
			$result['item_name'],
		);
	}

	/**
	 * Get a text link for this move.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForMove(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		MoveId $moveId,
	) : TextLinkMove {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();
		$moveId = $moveId->value();

		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`identifier` AS `vg_identifier`,
				`m`.`identifier` AS `move_identifier`,
				COALESCE(`md`.`name`, `mn`.`name`) AS `move_name`
			FROM `version_groups` AS `vg`
			INNER JOIN `moves` AS `m`
			INNER JOIN `move_names` AS `mn`
				ON `m`.`id` = `mn`.`move_id`
			LEFT JOIN `move_descriptions` AS `md`
				ON `md`.`version_group_id` = `vg`.`id`
				AND `md`.`language_id` = `mn`.`language_id`
				AND `md`.`move_id` = `m`.`id`
			WHERE `vg`.`id` = :version_group_id
				AND `m`.`id` = :move_id
				AND `mn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TextLinkNotFoundException(
				"No text link can be made with version group id $versionGroupId"
				. ", language id $languageId, and move id $moveId."
			);
		}

		return new TextLinkMove(
			$result['vg_identifier'],
			$result['move_identifier'],
			$result['move_name'],
		);
	}

	/**
	 * Get a text link for this Pokémon.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForPokemon(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		PokemonId $pokemonId,
	) : TextLinkPokemon {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();
		$pokemonId = $pokemonId->value();

		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`identifier` AS `vg_identifier`,
				`p`.`identifier` AS `pokemon_identifier`,
				`pn`.`name` AS `pokemon_name`
			FROM `version_groups` AS `vg`
			INNER JOIN `pokemon` AS `p`
			INNER JOIN `pokemon_names` AS `pn`
				ON `p`.`id` = `pn`.`pokemon_id`
			WHERE `vg`.`id` = :version_group_id
				AND `p`.`id` = :pokemon_id
				AND `pn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TextLinkNotFoundException(
				"No text link can be made with version group id $versionGroupId"
				. ", language id $languageId, and Pokémon id $pokemonId."
			);
		}

		return new TextLinkPokemon(
			$result['vg_identifier'],
			$result['pokemon_identifier'],
			$result['pokemon_name'],
		);
	}

	/**
	 * Get a text link for this type.
	 *
	 * @throws TextLinkNotFoundException if no text link can be made with these
	 *     parameters.
	 */
	public function getForType(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		TypeId $typeId,
	) : TextLinkType {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();
		$typeId = $typeId->value();

		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`identifier` AS `vg_identifier`,
				`t`.`identifier` AS `type_identifier`,
				`tn`.`name` AS `type_name`
			FROM `version_groups` AS `vg`
			INNER JOIN `types` AS `t`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			WHERE `vg`.`id` = :version_group_id
				AND `t`.`id` = :type_id
				AND `tn`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId, PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TextLinkNotFoundException(
				"No text link can be made with version group id $versionGroupId"
				. ", language id $languageId, and type id $typeId."
			);
		}

		return new TextLinkType(
			$result['vg_identifier'],
			$result['type_identifier'],
			$result['type_name'],
		);
	}

	/**
	 * Get a text link for the incense item, if any, that one of this Pokémon's
	 * parents must be holding.
	 */
	public function getForIncense(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		FormId $formId,
	) : ?TextLinkItem {
		$versionGroupId = $versionGroupId->value();
		$languageId = $languageId->value();
		$formId = $formId->value();

		$stmt = $this->db->prepare(
			'SELECT
				`vg`.`identifier` AS `vg_identifier`,
				`i`.`identifier` AS `item_identifier`,
				COALESCE(`id`.`name`, `in`.`name`) AS `item_name`
			FROM `evolutions_incense` AS `e`
			INNER JOIN `version_groups` AS `vg`
				ON `e`.`version_group_id` = `vg`.`id`
			INNER JOIN `items` AS `i`
				ON `e`.`item_id` = `i`.`id`
			INNER JOIN `item_names` AS `in`
				ON `i`.`id` = `in`.`item_id`
			LEFT JOIN `item_descriptions` AS `id`
				ON `id`.`version_group_id` = `vg`.`id`
				AND `id`.`language_id` = `in`.`language_id`
				AND `id`.`item_id` = `i`.`id`
			WHERE `e`.`version_group_id` = :version_group_id
				AND `e`.`form_id` = :form_id
				AND `in`.`language_id` = :language_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId, PDO::PARAM_INT);
		$stmt->bindValue(':form_id', $formId, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			return null;
		}

		return new TextLinkItem(
			$result['vg_identifier'],
			$result['item_identifier'],
			$result['item_name'],
		);
	}
}
