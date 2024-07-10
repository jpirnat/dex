<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final class EvolutionTreeToTable
{
	private array $rows = [];
	private array $cells = [];

	public function convert(EvolutionTree $tree) : array
	{
		$this->convertNodeToCell($tree);

		foreach ($tree->getEvolutions() ?? [] as $evolution) {
			$this->convert($evolution);
		}

		return $this->rows;
	}

	private function convertNodeToCell(EvolutionTree $tree) : void
	{
		$cell = new EvolutionTableCell(
			$tree->countBranches(),
			$tree->isFirstStage(),
			$tree->getIcon(),
			$tree->getIdentifier(),
			$tree->getName(),
			$tree->getMethods(),
		);

		$this->cells[] = $cell;

		if (!$tree->getEvolutions()) {
			$this->rows[] = new EvolutionTableRow($this->cells);
			$this->cells = [];
		}
	}
}
