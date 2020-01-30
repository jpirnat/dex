<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsMonth;

final class FormatData
{
	private string $formatIdentifier;
	private string $formatName;

	/** @var int[] $ratings */
	private array $ratings = [];

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
