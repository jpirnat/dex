<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Flags;

final readonly class DexFlag
{
	public function __construct(
		private string $identifier,
		private string $name,
		private string $description,
	) {}

	/**
	 * Get the flag's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the flag's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the flag's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
