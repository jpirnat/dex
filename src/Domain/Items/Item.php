<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class Item
{
	public function __construct(
		private ItemId $id,
		private string $identifier,
		private VersionGroupId $introducedInVersionGroupId,
	) {}

	/**
	 * Get the item's id.
	 */
	public function getId() : ItemId
	{
		return $this->id;
	}

	/**
	 * Get the item's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group id this item was introduced in.
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}
}
