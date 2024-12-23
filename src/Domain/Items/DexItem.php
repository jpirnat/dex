<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final readonly class DexItem
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) string $description,
	) {}
}
