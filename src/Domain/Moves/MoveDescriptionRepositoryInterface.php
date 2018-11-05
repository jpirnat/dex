<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

interface MoveDescriptionRepositoryInterface
{
	/**
	 * Get a move description by generation, language, and move.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 *
	 * @throws MoveDescriptionNotFoundException if no move description exists
	 *     for this generation, language, and move.
	 *
	 * @return MoveDescription
	 */
	public function getByGenerationAndLanguageAndMove(
		GenerationId $generationId,
		LanguageId $languageId,
		MoveId $moveId
	) : MoveDescription;
}
