<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class NamePercent
{
	private string $showdownName;
	private float $percent;

	/**
	 * Constructor.
	 *
	 * @param string $showdownName
	 * @param float $percent
	 */
	public function __construct(
		string $showdownName,
		float $percent
	) {
		// Clamp percent between 0 and 100.
		if ($percent < 0) {
			$percent = 0;
		}
		if ($percent > 100) {
			$percent = 100;
		}

		$this->showdownName = $showdownName;
		$this->percent = $percent;
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
