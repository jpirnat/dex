<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Leads;

use DateTimeInterface;
use Jp\Dex\Domain\Formats\FormatId;

interface LeadsRepositoryInterface
{
    /**
     * Does a leads record exist for this month and format?
     */
    public function has(DateTimeInterface $month, FormatId $formatId): bool;

    /**
     * Save a leads rated Pokémon record.
     */
    public function save(Leads $leads): void;
}
