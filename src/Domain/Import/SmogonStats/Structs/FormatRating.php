<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\SmogonStats\Structs;

final readonly class FormatRating
{
    public function __construct(
        private(set) string $showdownFormatName,
        private(set) int $rating,
    ) {}
}
