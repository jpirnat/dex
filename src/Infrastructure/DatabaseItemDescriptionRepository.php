<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\ItemDescription;
use Jp\Dex\Domain\Items\ItemDescriptionNotFoundException;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseItemDescriptionRepository implements ItemDescriptionRepositoryInterface
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
	 * Get an item description by generation, language, and item.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 *
	 * @throws ItemDescriptionNotFoundException if no item description exists
	 *     for this generation, language, and item.
	 *
	 * @return ItemDescription
	 */
	public function getByGenerationAndLanguageAndItem(
		GenerationId $generationId,
		LanguageId $languageId,
		ItemId $itemId
	) : ItemDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `item_descriptions`
			WHERE `generation_id` = :generation_id
				AND `language_id` = :language_id
				AND `item_id` = :item_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ItemDescriptionNotFoundException(
				'No item description exists with generation id '
				. $generationId->value() . ', language id '
				. $languageId->value() . ', and item id ' . $itemId->value()
				. '.'
			);
		}

		$itemDescription = new ItemDescription(
			$generationId,
			$languageId,
			$itemId,
			$result['description']
		);

		return $itemDescription;
	}
}
