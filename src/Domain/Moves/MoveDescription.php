<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;

class MoveDescription
{
	/** @var Generation $generation */
	private $generation;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var MoveId $moveId */
	private $moveId;

	/** @var string $description */
	private $description;

	/**
	 * Constructor.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 * @param string $description
	 */
	public function __construct(
		Generation $generation,
		LanguageId $languageId,
		MoveId $moveId,
		string $description
	) {
		$this->generation = $generation;
		$this->languageId = $languageId;
		$this->moveId = $moveId;
		$this->description = $description;
	}

	/**
	 * Get the move description's generation.
	 *
	 * @return Generation
	 */
	public function getGeneration() : Generation
	{
		return $this->generation;
	}

	/**
	 * Get the move description's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the move description's move id.
	 *
	 * @return MoveId
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the move description's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
