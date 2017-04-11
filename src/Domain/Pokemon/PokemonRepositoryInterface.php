<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Exception;

interface PokemonRepositoryInterface
{
	/**
	 * Get a Pokémon by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no Pokémon exists with this identifier.
	 *
	 * @return Pokemon
	 */
	public function getByIdentifier(string $identifier) : Pokemon;
}
