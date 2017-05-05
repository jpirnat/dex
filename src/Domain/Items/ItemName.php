<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;

class ItemName
{
	/** @var LanguageId $languageId */
	private $languageId;

	/** @var ItemId $itemId */
	private $itemId;

	/** @var string $name */
	private $name;

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
	public function languageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the item name's item id.
	 *
	 * @return ItemId
	 */
	public function itemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the item name's name value.
	 *
	 * @return string
	 */
	public function name() : string
	{
		return $this->name;
	}
}
