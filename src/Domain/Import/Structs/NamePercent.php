<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class NamePercent
{
	private float $percent;

	public function __construct(
		private string $showdownName,
		float $percent,
	) {
		// Clamp percent between 0 and 100.
		if ($percent < 0) {
			$percent = 0;
		}
		if ($percent > 100) {
			$percent = 100;
		}
		$this->percent = $percent;
	}

	/**
	 * Get the Pokémon Showdown entity name.
	 */
	public function showdownName() : string
	{
		return $this->showdownName;
	}

	/**
	 * Get the percent.
	 */
	public function percent() : float
	{
		return $this->percent;
	}
}
