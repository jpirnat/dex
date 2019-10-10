<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final class FormatRating
{
	/** @var string $showdownFormatName */
	private $showdownFormatName;

	/** @var int $rating */
	private $rating;

	/**
	 * Constructor.
	 *
	 * @param string $showdownFormatName
	 * @param int $rating
	 */
	public function __construct(
		string $showdownFormatName,
		int $rating
	) {
		$this->showdownFormatName = $showdownFormatName;
		$this->rating = $rating;
	}

	/**
	 * Get the Pokémon Showdown format name.
	 *
	 * @return string
	 */
	public function showdownFormatName() : string
	{
		return $this->showdownFormatName;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function rating() : int
	{
		return $this->rating;
	}
}
