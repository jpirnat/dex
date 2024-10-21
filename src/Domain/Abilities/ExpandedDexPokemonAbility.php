<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

final readonly class ExpandedDexPokemonAbility
{
	public function __construct(
		private string $identifier,
		private string $name,
		private string $description,
		private bool $isHiddenAbility,
	) {}

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

	public function isHiddenAbility() : bool
	{
		return $this->isHiddenAbility;
	}
}
