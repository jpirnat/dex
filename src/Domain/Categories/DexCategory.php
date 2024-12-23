<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

final readonly class DexCategory
{
	public function __construct(
		private(set) string $identifier,
		private(set) string $icon,
		private(set) string $name,
	) {}
}
