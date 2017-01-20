<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories;

use Exception;
use PDO;

class ShowdownPokemonRepository
{
	/** @var int[] $pokemonToImport */
	protected $pokemonToImport;

	/** @var ?int[] $pokemonToIgnore */
	protected $pokemonToIgnore;

	/** @var string[] $unknownPokemon */
	protected $unknownPokemon = [];

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$stmt = $db->prepare(
			'SELECT
				`name`,
				`pokemon_id`
			FROM `showdown_pokemon_to_import`'
		);
		$stmt->execute();
		$this->pokemonToImport = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`pokemon_id`
			FROM `showdown_pokemon_to_ignore`'
		);
		$stmt->execute();
		$this->pokemonToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown Pokémon name known and imported?
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return bool
	 */
	public function isImported(string $showdownPokemonName) : bool
	{
		return isset($this->pokemonToImport[$showdownPokemonName]);
	}

	/**
	 * Is the Pokémon Showdown Pokémon name known and ignored?
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return bool
	 */
	public function isIgnored(string $showdownPokemonName) : bool
	{
		return isset($this->pokemonToIgnore[$showdownPokemonName]);
	}

	/**
	 * Is the Pokémon Showdown Pokémon name known?
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return bool
	 */
	public function isKnown(string $showdownPokemonName) : bool
	{
		return isset($this->pokemonToImport[$showdownPokemonName])
			|| isset($this->pokemonToIgnore[$showdownPokemonName])
		;
	}

	/**
	 * Add a Pokémon Showdown Pokémon name to the list of unknown Pokémon.
	 *
	 * @param string $showdownPokemonName
	 *
	 * @return void
	 */
	public function addUnknown(string $showdownPokemonName) : void
	{
		$this->unknownPokemon[$showdownPokemonName] = $showdownPokemonName;
	}

	/**
	 * Get the Pokémon id of a Pokémon Showdown Pokemon name.
	 *
	 * @param string $showdownPokemonName
	 *
	 * @throws Exception if $showdownPokemonName is not an imported name.
	 *
	 * @return int
	 */
	public function getPokemonId(string $showdownPokemonName) : int
	{
		// If the Pokémon is imported, return the Pokémon id.
		if ($this->isImported($showdownPokemonName)) {
			return $this->pokemonToImport[$showdownPokemonName];
		}

		// If the Pokémon is not known, add it to the list of unknown Pokémon.
		if (!$this->isKnown($showdownPokemonName)) {
			$this->addUnknown($showdownPokemonName);
		}

		throw new Exception('Pokémon should not be imported: ' . $showdownPokemonName);
	}
}
