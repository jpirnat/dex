<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final readonly class DexItem
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private string $description,
	) {}

	public function getIcon() : string
	{
		return $this->icon;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getDescription() : string
	{
		return $this->description;
	}
}
