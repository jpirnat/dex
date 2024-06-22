<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class ItemDescription
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private LanguageId $languageId,
		private ItemId $itemId,
		private string $name,
		private string $description,
	) {}

	/**
	 * Get the item description's version group id.
	 */
	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
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
	 * Get the item's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the item's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
