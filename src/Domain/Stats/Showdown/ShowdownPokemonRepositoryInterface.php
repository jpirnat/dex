<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Showdown;

use Jp\Dex\Domain\Pokemon\PokemonId;

interface ShowdownPokemonRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown Pokémon name known and imported?
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownPokemonName) : bool;

	/**
	 * Is the Pokémon Showdown Pokémon name known and ignored?
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownPokemonName) : bool;

	/**
	 * Is the Pokémon Showdown Pokémon name known?
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownPokemonName) : bool;

	/**
	 * Add a Pokémon Showdown Pokémon name to the list of unknown Pokémon.
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownPokemonName) : void;

	/**
	 * Get the Pokémon id of a Pokémon Showdown Pokemon name.
	 *
	 * @param string $showdownPokemonName
	 *
	 * @throws PokemonNotImportedException if $showdownPokemonName is not an
	 *     imported Pokémon name.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId(string $showdownPokemonName) : PokemonId;

	/**
	 * Get the names of the unknown Pokémon the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
