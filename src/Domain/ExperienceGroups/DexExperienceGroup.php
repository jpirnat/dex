<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\ExperienceGroups;

final readonly class DexExperienceGroup
{
	public function __construct(
		private string $identifier,
		private string $name,
		private int $points,
	) {}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getPoints() : int
	{
		return $this->points;
	}
}
