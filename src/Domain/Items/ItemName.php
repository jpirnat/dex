<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;

final class ItemName
{
	private LanguageId $languageId;
	private ItemId $itemId;
	private string $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		ItemId $itemId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->itemId = $itemId;
		$this->name = $name;
	}

	/**
	 * Get the item name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the item name's item id.
	 *
	 * @return ItemId
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the item name's name value.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
