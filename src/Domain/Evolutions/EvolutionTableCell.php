<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTableCell
{
	public function __construct(
		private int $rowspan,
		private bool $isFirstStage,
		private string $icon,
		private string $identifier,
		private string $name,
		/** @var EvolutionTableMethod[] $methods */ private array $methods,
	) {}

	public function getRowspan() : int
	{
		return $this->rowspan;
	}

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
}
