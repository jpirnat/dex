<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class MoveMethodName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) MoveMethodId $moveMethodId,
		private(set) string $name,
		private(set) string $description,
	) {}
}
