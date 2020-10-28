<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

final class DexType
{
	public function __construct(
		private TypeId $id,
		private string $identifier,
		private string $icon,
		private string $name,
	) {}

	/**
	 * Get the type's id.
	 *
	 * @return TypeId
	 */
	public function getId() : TypeId
	{
		return $this->id;
	}

	/**
	 * Get the type's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the type's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the type's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
