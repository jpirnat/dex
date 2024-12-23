<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;

final readonly class Format
{
	public function __construct(
		private(set) FormatId $id,
		private(set) string $identifier,
		private(set) string $name,
		private(set) GenerationId $generationId,
		private(set) VersionGroupId $versionGroupId,
		private(set) int $level,
		private(set) int $fieldSize,
		private(set) string $smogonDexIdentifier,
	) {}
}
