<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\EggGroups;

final readonly class DexEggGroup
{
	public function __construct(
		private string $identifier,
		private string $name,
	) {}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getName() : string
	{
		return $this->name;
	}
}
