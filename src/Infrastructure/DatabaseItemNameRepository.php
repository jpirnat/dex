<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\ItemName;
use Jp\Dex\Domain\Items\ItemNameNotFoundException;
use Jp\Dex\Domain\Items\ItemNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use PDO;

final readonly class DatabaseItemNameRepository implements ItemNameRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get an item name by language and item.
	 *
	 * @throws ItemNameNotFoundException if no item name exists for this
	 *     language and item.
	 */
	public function getByLanguageAndItem(
		LanguageId $languageId,
		ItemId $itemId,
	) : ItemName {
		$stmt = $this->db->prepare(
			'SELECT
				`name`
			FROM `item_names`
			WHERE `language_id` = :language_id
				AND `item_id` = :item_id
			LIMIT 1'
		);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new ItemNameNotFoundException(
				"No item name exists with language id $languageId->value and item id $itemId->value."
			);
		}

		return new ItemName(
			$languageId,
			$itemId,
			$result['name'],
		);
	}
}
