<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTree
{
	public function __construct(
		private bool $isFirstStage,
		private string $icon,
		private string $identifier,
		private string $name,
		/** @var EvolutionTableMethod[] */ private array $methods,
		/** @var self[] */ private array $evolutions,
	) {}

	public function isFirstStage() : bool
	{
		return $this->isFirstStage;
	}

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

	/**
	 * @return EvolutionTableMethod[]
	 */
	public function getMethods() : array
	{
		return $this->methods;
	}

	/**
	 * @return self[]
	 */
	public function getEvolutions() : array
	{
		return $this->evolutions;
	}

	public function countBranches() : int
	{
		if (!$this->evolutions) {
			return 1;
		}

		$branches = 0;

		foreach ($this->evolutions as $evolution) {
			$branches += $evolution->countBranches();
		}

		return $branches;
	}
}
