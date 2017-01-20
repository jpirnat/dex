<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Structs;

class NamePercent
{
	/** @var string $showdownName */
	protected $showdownName;

	/** @var float $percent */
	protected $percent;

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
