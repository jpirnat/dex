<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\BreedingChains;

use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Versions\DexVersionGroup;

final readonly class BreedingChainRecord
{
	public function __construct(
		private string $icon,
		private string $identifier,
		private string $name,
		private DexVersionGroup $versionGroup,
		/** @var DexEggGroup[] $eggGroups */ private array $eggGroups,
		private GenderRatio $genderRatio,
		private int $eggCycles,
		private int $stepsToHatch,
		private string $moveMethod,
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

	public function getVersionGroup() : DexVersionGroup
	{
		return $this->versionGroup;
	}

	/**
	 * @return DexEggGroup[]
	 */
	public function getEggGroups() : array
	{
		return $this->eggGroups;
	}

	public function getGenderRatio() : GenderRatio
	{
		return $this->genderRatio;
	}

	public function getEggCycles() : int
	{
		return $this->eggCycles;
	}

	public function getStepsToHatch() : int
	{
		return $this->stepsToHatch;
	}

	public function getMoveMethod() : string
	{
		return $this->moveMethod;
	}
}
