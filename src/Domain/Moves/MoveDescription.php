<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class MoveDescription
{
	private GenerationId $generationId;
	private LanguageId $languageId;
	private MoveId $moveId;
	private string $description;

	/**
	 * Constructor.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 * @param string $description
	 */
	public function __construct(
		GenerationId $generationId,
		LanguageId $languageId,
		MoveId $moveId,
		string $description
	) {
		$this->generationId = $generationId;
		$this->languageId = $languageId;
		$this->moveId = $moveId;
		$this->description = $description;
	}

	/**
	 * Get the move description's generation.
	 *
	 * @return GenerationId
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
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
