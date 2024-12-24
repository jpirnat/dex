<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final readonly class Generation
{
	public function __construct(
		private(set) GenerationId $id,
		private(set) string $identifier,
		private(set) string $smogonDexIdentifier,
	) {}
}
