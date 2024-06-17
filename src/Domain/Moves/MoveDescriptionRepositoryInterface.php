<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\VersionGroupId;

interface MoveDescriptionRepositoryInterface
{
	/**
	 * Get a move description by version group, language, and move.
	 */
	public function getByMove(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
		MoveId $moveId,
	) : MoveDescription;
}
