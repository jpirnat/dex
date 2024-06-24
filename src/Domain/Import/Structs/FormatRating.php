<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Structs;

final readonly class FormatRating
{
	public function __construct(
		private string $showdownFormatName,
		private int $rating,
	) {}

	/**
	 * Get the Pokémon Showdown format name.
	 */
	public function showdownFormatName() : string
	{
		return $this->showdownFormatName;
	}

	/**
	 * Get the rating.
	 */
	public function rating() : int
	{
		return $this->rating;
	}
}
