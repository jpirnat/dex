<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Import\Showdown\ItemNotImportedException;
use Jp\Dex\Domain\Import\Showdown\ShowdownItemRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use PDO;

final class DatabaseShowdownItemRepository implements ShowdownItemRepositoryInterface
{
	/** @var ItemId[] $itemsToImport */
	private array $itemsToImport = [];

	/** @var ?ItemId[] $itemsToIgnore */
	private array $itemsToIgnore = [];

	/** @var string[] $unknownItems */
	private array $unknownItems = [];

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$stmt = $db->prepare(
			'SELECT
				`name`,
				`item_id`
			FROM `showdown_items_to_import`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->itemsToImport[$result['name']] = new ItemId($result['item_id']);
		}

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`item_id`
			FROM `showdown_items_to_ignore`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($result['item_id'] !== null) {
				// The Pokémon Showdown item name has an item id.
				$itemId = new ItemId($result['item_id']);
			} else {
				$itemId = null;
			}

			$this->itemsToIgnore[$result['name']] = $itemId;
		}
	}

	/**
	 * Is the Pokémon Showdown item name known and imported?
	 *
	 * @param string $showdownItemName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownItemName) : bool
	{
		return isset($this->itemsToImport[$showdownItemName]);
	}

	/**
	 * Is the Pokémon Showdown item name known and ignored?
	 *
	 * @param string $showdownItemName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownItemName) : bool
	{
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return array_key_exists($showdownItemName, $this->itemsToIgnore);
	}

	/**
	 * Is the Pokémon Showdown item name known?
	 *
	 * @param string $showdownItemName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownItemName) : bool
	{
		return $this->isImported($showdownItemName)
			|| $this->isIgnored($showdownItemName)
		;
	}

	/**
	 * Add a Pokémon Showdown item name to the list of unknown items.
	 *
	 * @param string $showdownItemName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownItemName) : void
	{
		$this->unknownItems[$showdownItemName] = $showdownItemName;
	}

	/**
	 * Get the item id of a Pokémon Showdown item name.
	 *
	 * @param string $showdownItemName
	 *
	 * @throws ItemNotImportedException if $showdownItemName is not an imported
	 *     item name.
	 *
	 * @return ItemId
	 */
	public function getItemId(string $showdownItemName) : ItemId
	{
		// If the item is imported, return the item id.
		if ($this->isImported($showdownItemName)) {
			return $this->itemsToImport[$showdownItemName];
		}

		// If the item is not known, add it to the list of unknown items.
		if (!$this->isKnown($showdownItemName)) {
			$this->addUnknown($showdownItemName);
		}

		throw new ItemNotImportedException(
			'Item should not be imported: ' . $showdownItemName
		);
	}

	/**
	 * Get the names of the unknown items the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownItems;
	}
}
