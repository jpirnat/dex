<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final readonly class DexVersionGroup
{
	public function __construct(
		private(set) VersionGroupId $id,
		private(set) string $identifier,
		private(set) GenerationId $generationId,
		private(set) string $name,
		/** @var DexVersion[] $versions */
		private(set) array $versions,
	) {}
}
