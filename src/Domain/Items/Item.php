<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

use Jp\Dex\Domain\Versions\VersionGroupId;

final class Item
{
	private ItemId $id;
	private string $identifier;
	private VersionGroupId $introducedInVersionGroupId;

	/**
	 * Constructor.
	 *
	 * @param ItemId $itemId
	 * @param string $identifier
	 * @param VersionGroupId $introducedInVersionGroupId
	 */
	public function __construct(
		ItemId $itemId,
		string $identifier,
		VersionGroupId $introducedInVersionGroupId
	) {
		$this->id = $itemId;
		$this->identifier = $identifier;
		$this->introducedInVersionGroupId = $introducedInVersionGroupId;
	}

	/**
	 * Get the item's id.
	 *
	 * @return ItemId
	 */
	public function getId() : ItemId
	{
		return $this->id;
	}

	/**
	 * Get the item's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the version group id this item was introduced in.
	 *
	 * @return VersionGroupId
	 */
	public function getIntroducedInVersionGroupId() : VersionGroupId
	{
		return $this->introducedInVersionGroupId;
	}
}
