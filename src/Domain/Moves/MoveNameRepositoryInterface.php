<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;

interface MoveNameRepositoryInterface
{
	/**
	 * Get a move name by language and move.
	 *
	 * @throws MoveNameNotFoundException if no move name exists for this
	 *     language and move.
	 */
	public function getByLanguageAndMove(
		LanguageId $languageId,
		MoveId $moveId,
	) : MoveName;
}
