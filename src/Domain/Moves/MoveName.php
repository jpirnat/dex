<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Moves;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class MoveName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) MoveId $moveId,
		private(set) string $name,
	) {}
}
