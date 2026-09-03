<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use DateTimeInterface;

final readonly class MonthControlFormatter
{
    /**
     * Format the prev month or next month for the month control.
     */
    public function format(?DateTimeInterface $month, IntlFormatter $formatter): ?array
    {
        if ($month === null) {
            return null;
        }

        return [
            'value' => $month->format('Y-m'),
            'name' => $formatter->formatMonth($month),
        ];
    }
}
