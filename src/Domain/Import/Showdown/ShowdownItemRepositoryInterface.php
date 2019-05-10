<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Items\ItemId;

interface ShowdownItemRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown item name known and imported?
	 *
	 * @param string $showdownItemName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownItemName) : bool;

	/**
	 * Is the Pokémon Showdown item name known and ignored?
	 *
	 * @param string $showdownItemName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownItemName) : bool;

	/**
	 * Is the Pokémon Showdown item name known?
	 *
	 * @param string $showdownItemName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownItemName) : bool;

	/**
	 * Add a Pokémon Showdown item name to the list of unknown items.
	 *
	 * @param string $showdownItemName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownItemName) : void;

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
	public function getItemId(string $showdownItemName) : ItemId;

	/**
	 * Get the names of the unknown items the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
