<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

interface MoveDescriptionRepositoryInterface
{
	/**
	 * Get a move description by generation, language, and move.
	 */
	public function getByGenerationAndLanguageAndMove(
		GenerationId $generationId,
		LanguageId $languageId,
		MoveId $moveId
	) : MoveDescription;
}
