<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class MoveDescription
{
	public function __construct(
		private GenerationId $generationId,
		private LanguageId $languageId,
		private MoveId $moveId,
		private string $description,
	) {}

	/**
	 * Get the move description's generation.
	 */
	public function getGenerationId() : GenerationId
	{
		return $this->generationId;
	}

	/**
	 * Get the move description's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the move description's move id.
	 */
	public function getMoveId() : MoveId
	{
		return $this->moveId;
	}

	/**
	 * Get the move description's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
