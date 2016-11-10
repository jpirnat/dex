<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers\Structs;

class FormatRating
{
	/** @var string $smogonFormatName */
	protected $smogonFormatName;

	/** @var int $rating */
	protected $rating;

	/**
	 * Constructor.
	 *
	 * @param string $smogonFormatName
	 * @param int $rating
	 */
	public function __construct(
		string $smogonFormatName,
		int $rating
	) {
		$this->smogonFormatName = $smogonFormatName;
		$this->rating = $rating;
	}

	/**
	 * Get the Smogon format name.
	 *
	 * @return string
	 */
	public function smogonFormatName() : string
	{
		return $this->smogonFormatName;
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
