<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

final readonly class StatsPokemonTeraType
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private float $percent,
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

	public function getPercent() : float
	{
		return $this->percent;
	}
}
