<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsMonth;

class FormatData
{
	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var string $formatName */
	private $formatName;

	/** @var int[] $ratings */
	private $ratings = [];

	/**
	 * Constructor.
	 *
	 * @param string $formatIdentifier
	 * @param string $formatName
	 * @param int[] $ratings
	 */
	public function __construct(
		string $formatIdentifier,
		string $formatName,
		array $ratings
	) {
		$this->formatIdentifier = $formatIdentifier;
		$this->formatName = $formatName;
		$this->ratings = $ratings;
	}

	/**
	 * Get the format's identifier.
	 *
	 * @return string
	 */
	public function getFormatIdentifier() : string
	{
		return $this->formatIdentifier;
	}

	/**
	 * Get the format's name.
	 *
	 * @return string
	 */
	public function getFormatName() : string
	{
		return $this->formatName;
	}

	/**
	 * Get the format's ratings.
	 *
	 * @return int[]
	 */
	public function getRatings() : array
	{
		return $this->ratings;
	}
}
