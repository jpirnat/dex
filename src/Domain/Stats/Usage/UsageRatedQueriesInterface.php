<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface UsageRatedQueriesInterface
{
    /**
     * Get the format/rating combinations for this month.
     *
     * @return array An array of the form [
     *     [
     *         'formatId' => FormatId
     *         'rating' => int
     *     ],
     *     ...
     * ]
     */
    public function getFormatRatings(DateTimeInterface $month): array;

    /**
     * Get the months that have data recorded for this format and rating.
     *
     * @return DateTime[]
     */
    public function getMonthsWithData(FormatId $formatId, int $rating): array;
}
