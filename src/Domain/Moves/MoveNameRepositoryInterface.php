<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;

interface MoveNameRepositoryInterface
{
	/**
	 * Get a move name by language and move.
	 *
	 * @param LanguageId $languageId
	 * @param MoveId $moveId
	 *
	 * @throws MoveNameNotFoundException if no move name exists for this
	 *     language and move.
	 *
	 * @return MoveName
	 */
	public function getByLanguageAndMove(
		LanguageId $languageId,
		MoveId $moveId
	) : MoveName;
}
