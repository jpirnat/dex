<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Showdown;

use Jp\Dex\Domain\Pokemon\PokemonId;

interface ShowdownPokemonRepositoryInterface
{
	/**
	 * Is the Pokémon Showdown Pokémon name known and imported?
	 */
	public function isImported(string $showdownPokemonName) : bool;

	/**
	 * Is the Pokémon Showdown Pokémon name known and ignored?
	 */
	public function isIgnored(string $showdownPokemonName) : bool;

	/**
	 * Is the Pokémon Showdown Pokémon name known?
	 */
	public function isKnown(string $showdownPokemonName) : bool;

	/**
	 * Add a Pokémon Showdown Pokémon name to the list of unknown Pokémon.
	 */
	public function addUnknown(string $showdownPokemonName) : void;

	/**
	 * Get the Pokémon id of a Pokémon Showdown Pokemon name.
	 *
	 * @throws PokemonNotImportedException if $showdownPokemonName is not an
	 *     imported Pokémon name.
	 */
	public function getPokemonId(string $showdownPokemonName) : PokemonId;

	/**
	 * Get the names of the unknown Pokémon the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array;
}
