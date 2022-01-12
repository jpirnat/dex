<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class ItemDescription
{
	public function __construct(
		private GenerationId $generationId,
		private LanguageId $languageId,
		private ItemId $itemId,
		private string $description,
	) {}

	/**
	 * Get the item description's generation id.
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the item description's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the item description's item id.
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the item description's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
