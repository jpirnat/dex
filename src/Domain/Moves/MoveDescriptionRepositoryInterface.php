<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\Generation;

interface MoveDescriptionRepositoryInterface
{
	/**
	 * Get a move description by generation, language, and move.
	 *
	 * @param Generation $generation
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 *
	 * @throws MoveDescriptionNotFoundException if no move description exists
	 *     for this generation, language, and move.
	 *
	 * @return MoveDescription
	 */
	public function getByGenerationAndLanguageAndMove(
		Generation $generation,
		LanguageId $languageId,
		MoveId $moveId
	) : MoveDescription;
}
