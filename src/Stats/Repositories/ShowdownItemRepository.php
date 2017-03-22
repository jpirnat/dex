<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownItemRepository
{
	/** @var int[] $itemsToImport */
	protected $itemsToImport = [];

	/** @var ?int[] $itemsToIgnore */
	protected $itemsToIgnore = [];

	/** @var string[] $unknownItems */
	protected $unknownItems = [];

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
		$this->itemsToImport = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`item_id`
			FROM `showdown_items_to_ignore`'
		);
		$stmt->execute();
		$this->itemsToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
	 * @throws Exception if $showdownItemName is not an imported name.
	 *
	 * @return int
	 */
	public function getItemId(string $showdownItemName) : int
	{
		// If the item is imported, return the item id.
		if ($this->isImported($showdownItemName)) {
			return $this->itemsToImport[$showdownItemName];
		}

		// If the item is not known, add it to the list of unknown items.
		if (!$this->isKnown($showdownItemName)) {
			$this->addUnknown($showdownItemName);
		}

		throw new Exception('Item should not be imported: ' . $showdownItemName);
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
