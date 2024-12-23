<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Models;

use Jp\Dex\Domain\Forms\FormId;

final readonly class Model
{
	public function __construct(
		private(set) FormId $formId,
		private(set) bool $isShiny,
		private(set) bool $isBack,
		private(set) bool $isFemale,
		private(set) int $attackingIndex,
		private(set) string $image,
	) {}
}
