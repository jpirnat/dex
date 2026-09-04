<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Usage;

use DateTime;
use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface MonthQueriesInterface
{
    /**
     * Get the previous month with usage data for any format.
     */
    public function getPrev(DateTimeInterface $month): ?DateTime;

    /**
     * Get the next month with usage data for any format.
     */
    public function getNext(DateTimeInterface $month): ?DateTime;

    /**
     * Get the previous month with usage data for this format.
     */
    public function getPrevByFormat(DateTimeInterface $month, FormatId $formatId): ?DateTime;

    /**
     * Get the next month with usage data for this format.
     */
    public function getNextByFormat(DateTimeInterface $month, FormatId $formatId): ?DateTime;
}
