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

	/** @var array<string, int> $itemsToIgnore */
	private array $itemsToIgnore = [];

	/** @var string[] $unknownItems */
	private array $unknownItems = [];


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
				1
			FROM `showdown_items_to_ignore`'
		);
		$stmt->execute();
		$this->itemsToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown item name known and imported?
	 */
	public function isImported(string $showdownItemName) : bool
	{
		return isset($this->itemsToImport[$showdownItemName]);
	}

	/**
	 * Is the Pokémon Showdown item name known and ignored?
	 */
	public function isIgnored(string $showdownItemName) : bool
	{
		return isset($this->itemsToIgnore[$showdownItemName]);
	}

	/**
	 * Is the Pokémon Showdown item name known?
	 */
	public function isKnown(string $showdownItemName) : bool
	{
		return $this->isImported($showdownItemName)
			|| $this->isIgnored($showdownItemName)
		;
	}

	/**
	 * Add a Pokémon Showdown item name to the list of unknown items.
	 */
	public function addUnknown(string $showdownItemName) : void
	{
		$this->unknownItems[$showdownItemName] = $showdownItemName;
	}

	/**
	 * Get the item id of a Pokémon Showdown item name.
	 *
	 * @throws ItemNotImportedException if $showdownItemName is not an imported
	 *     item name.
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
