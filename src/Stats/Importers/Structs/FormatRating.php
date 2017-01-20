<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers\Structs;

class FormatRating
{
	/** @var string $showdownFormatName */
	protected $showdownFormatName;

	/** @var int $rating */
	protected $rating;

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
