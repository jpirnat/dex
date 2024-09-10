<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Moves\Targets\TargetId;
use Jp\Dex\Domain\Types\DexType;

final readonly class StatsPokemonMove
{
	public function __construct(
		private string $identifier,
		private string $name,
		private float $percent,
		private float $change,
		private DexType $type,
		private DexCategory $category,
		private int $pp,
		private int $power,
		private int $accuracy,
		private int $priority,
		private TargetId $targetId,
	) {}

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

	public function getChange() : float
	{
		return $this->change;
	}

	public function getType() : DexType
	{
		return $this->type;
	}

	public function getCategory() : DexCategory
	{
		return $this->category;
	}

	public function getPP() : int
	{
		return $this->pp;
	}

	public function getPower() : int
	{
		return $this->power;
	}

	public function getAccuracy() : int
	{
		return $this->accuracy;
	}

	public function getPriority() : int
	{
		return $this->priority;
	}

	public function getTargetId() : TargetId
	{
		return $this->targetId;
	}
}
