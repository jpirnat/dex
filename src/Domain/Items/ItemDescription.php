<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

class ItemDescription
{
	/** @var GenerationId $generationId */
	private $generationId;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var ItemId $itemId */
	private $itemId;

	/** @var string $description */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param ItemId $itemId
	 * @param string $description
	 */
	public function __construct(
		GenerationId $generationId,
		LanguageId $languageId,
		ItemId $itemId,
		string $description
	) {
		$this->generationId = $generationId;
		$this->languageId = $languageId;
		$this->itemId = $itemId;
		$this->description = $description;
	}

	/**
	 * Get the item description's generation id.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
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
