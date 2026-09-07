<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Pokemon\PokemonNameNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use PDO;

final readonly class DatabasePokemonNameRepository implements PokemonNameRepositoryInterface
{
    public function __construct(
        private PDO $db,
    ) {}

    /**
     * Get a Pokémon name by language and Pokémon.
     *
     * @throws PokemonNameNotFoundException if no Pokémon name exists for this
     *     language and Pokémon.
     */
    public function getByLanguageAndPokemon(
        LanguageId $languageId,
        PokemonId $pokemonId,
    ): PokemonName {
        $stmt = $this->db->prepare(
            'SELECT
                `name`
            FROM `pokemon_names`
            WHERE `language_id` = :language_id
                AND `pokemon_id` = :pokemon_id
            LIMIT 1'
        );
        $stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
        $stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new PokemonNameNotFoundException(
                "No Pokémon name exists with language id $languageId->value and Pokémon id $pokemonId->value."
            );
        }

        return new PokemonName(
            $languageId,
            $pokemonId,
            $result['name'],
        );
    }
}
