<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\ItemDescription;
use Jp\Dex\Domain\Items\ItemDescriptionNotFoundException;
use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;
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
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 *
	 * @throws ItemDescriptionNotFoundException if no item description exists
	 *     for this generation, language, and item.
	 *
	 * @return ItemDescription
	 */
	public function getByGenerationAndLanguageAndItem(
		Generation $generation,
		LanguageId $languageId,
		ItemId $itemId
	) : ItemDescription {
		$stmt = $this->db->prepare(
			'SELECT
				`description`
			FROM `item_descriptions`
			WHERE `generation` = :generation
				AND `language_id` = :language_id
				AND `item_id` = :item_id
			LIMIT 1'
		);
		$stmt->bindValue(':generation', $generation->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ItemDescriptionNotFoundException(
				'No item description exists with generation '
				. $generation->value() . ', language id '
				. $languageId->value() . ', and item id ' . $itemId->value()
				. '.'
			);
		}

		$itemDescription = new ItemDescription(
			$generation,
			$languageId,
			$itemId,
			$result['description']
		);

		return $itemDescription;
	}
}
