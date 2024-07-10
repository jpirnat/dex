<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTableRow
{
	public function __construct(
		/** @var EvolutionTableCell[] $cells */ private array $cells,
	) {}

	/**
	 * @return EvolutionTableCell[]
	 */
	public function getCells() : array
	{
		return $this->cells;
	}
}
