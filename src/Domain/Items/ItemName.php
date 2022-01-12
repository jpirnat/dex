<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;

final class ItemName
{
	public function __construct(
		private LanguageId $languageId,
		private ItemId $itemId,
		private string $name,
	) {}

	/**
	 * Get the item name's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the item name's item id.
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the item name's name value.
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
