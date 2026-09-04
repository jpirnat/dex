<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Leads;

use DateTimeImmutable;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidCountException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\BattleData\ValidateMonthTrait;
use Jp\Dex\Domain\Formats\FormatId;

final readonly class Leads
{
    use ValidateMonthTrait;

    /**
     * Constructor.
     *
     * @throws InvalidMonthException if $month is invalid.
     * @throws InvalidCountException if $totalLeads is invalid.
     */
    public function __construct(
        private(set) DateTimeImmutable $month,
        private(set) FormatId $formatId,
        private(set) int $totalLeads,
    ) {
        $this->validateMonth($month);

        if ($totalLeads < 0) {
            throw new InvalidCountException("Invalid number of total leads: $totalLeads.");
        }
    }
}
