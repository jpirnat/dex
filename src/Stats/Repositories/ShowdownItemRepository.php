<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownItemRepository
{
	/** @var int[] $itemsToImport */
	protected $itemsToImport;

	/** @var ?int[] $itemsToIgnore */
	protected $itemsToIgnore;

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
		return isset($this->itemsToIgnore[$showdownItemName]);
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
		return isset($this->itemsToImport[$showdownItemName])
			|| isset($this->itemsToIgnore[$showdownItemName])
		;
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
}
