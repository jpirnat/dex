<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BattleData\Moveset;

use DateTimeImmutable;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidCountException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\BattleData\Exceptions\InvalidViabilityCeilingException;
use Jp\Dex\Domain\BattleData\ValidateMonthTrait;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

final readonly class MovesetPokemon
{
    use ValidateMonthTrait;

    /**
     * Constructor.
     *
     * @throws InvalidMonthException if $month is invalid.
     * @throws InvalidCountException if $rawCount is invalid.
     * @throws InvalidViabilityCeilingException if $viabilityCeiling is invalid.
     */
    public function __construct(
        private(set) DateTimeImmutable $month,
        private(set) FormatId $formatId,
        private(set) PokemonId $pokemonId,
        private(set) int $rawCount,
        private(set) ?int $viabilityCeiling,
    ) {
        $this->validateMonth($month);

        if ($rawCount < 0) {
            throw new InvalidCountException("Invalid raw count: $rawCount.");
        }

        if ($viabilityCeiling !== null && $viabilityCeiling < 0) {
            throw new InvalidViabilityCeilingException(
                "Invalid viability ceiling: $viabilityCeiling."
            );
        }
    }
}
