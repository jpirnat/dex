<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

final readonly class DexCategory
{
	public function __construct(
		private string $identifier,
		private string $icon,
		private string $name,
	) {}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getIcon() : string
	{
		return $this->icon;
	}

	public function getName() : string
	{
		return $this->name;
	}
}
