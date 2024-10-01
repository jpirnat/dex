<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

final readonly class DexType
{
	public function __construct(
		private string $identifier,
		private string $name,
		private string $icon,
	) {}

	/**
	 * Get the type's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the type's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the type's icon.
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}
}
