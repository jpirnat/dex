<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Domain\Evolutions\EvolutionTableCell;
use Jp\Dex\Domain\Evolutions\EvolutionTableMethod;
use Jp\Dex\Domain\Evolutions\EvolutionTableRow;

final readonly class EvolutionTableFormatter
{
	/**
	 * @param EvolutionTableRow[] $rows
	 */
	public function formatRows(array $rows) : array
	{
		$r = [];

		foreach ($rows as $row) {
			$r[] = $this->formatRow($row);
		}

		return $r;
	}

	public function formatRow(EvolutionTableRow $row) : array
	{
		return [
			'cells' => $this->formatCells($row->cells),
		];
	}

	/**
	 * @param EvolutionTableCell[] $cells
	 */
	private function formatCells(array $cells) : array
	{
		$c = [];

		foreach ($cells as $cell) {
			$c[] = $this->formatCell($cell);
		}

		return $c;
	}

	private function formatCell(EvolutionTableCell $cell) : array
	{
		return [
			'rowspan' => $cell->rowspan,
			'isFirstStage' => $cell->isFirstStage,
			'icon' => $cell->icon,
			'identifier' => $cell->identifier,
			'name' => $cell->name,
			'methods' => $this->formatMethods($cell->methods),
		];
	}

	/**
	 * @param EvolutionTableMethod[] $methods
	 */
	private function formatMethods(array $methods) : array
	{
		$m = [];

		foreach ($methods as $method) {
			$m[] = $this->formatMethod($method);
		}

		return $m;
	}

	private function formatMethod(EvolutionTableMethod $method) : array
	{
		return [
			'html' => $method->html,
		];
	}
}
