<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Types;

final readonly class DexType
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $name,
		private(set) string $icon,
	) {}
}
