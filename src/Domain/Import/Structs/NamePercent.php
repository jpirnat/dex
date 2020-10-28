<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class NamePercent
{
	public function __construct(
		private string $showdownName,
		private float $percent,
	) {
		// Clamp percent between 0 and 100.
		if ($this->percent < 0) {
			$this->percent = 0;
		}
		if ($this->percent > 100) {
			$this->percent = 100;
		}
	}

	/**
	 * Get the Pokémon Showdown entity name.
	 *
	 * @return string
	 */
	public function showdownName() : string
	{
		return $this->showdownName;
	}

	/**
	 * Get the percent.
	 *
	 * @return float
	 */
	public function percent() : float
	{
		return $this->percent;
	}
}
