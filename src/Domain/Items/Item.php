<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Items;

final readonly class Item
{
	public function __construct(
		private(set) ItemId $id,
		private(set) string $identifier,
	) {}
}
