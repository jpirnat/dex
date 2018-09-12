<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;

class ItemDescription
{
	/** @var Generation $generation */
	private $generation;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var ItemId $itemId */
	private $itemId;

	/** @var string $description */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 * @param string $description
	 */
	public function __construct(
		Generation $generation,
		LanguageId $languageId,
		ItemId $itemId,
		string $description
	) {
		$this->generation = $generation;
		$this->languageId = $languageId;
		$this->itemId = $itemId;
		$this->description = $description;
	}

	/**
	 * Get the item description's generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the item description's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the item description's item id.
	 *
	 * @return ItemId
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the item description's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
