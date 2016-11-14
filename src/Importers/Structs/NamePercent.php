<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Structs;

class NamePercent
{
	/** @var string $name */
	protected $name;

	/** @var float $percent */
	protected $percent;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param float $percent
	 */
	public function __construct(
		string $name,
		float $percent
	) {
		$this->name = $name;
		$this->percent = $percent;
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function name() : string
	{
		return $this->name;
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
