<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Showdown\PokemonNotImportedException;
use Jp\Dex\Domain\Stats\Showdown\ShowdownPokemonRepositoryInterface;
use PDO;

class DatabaseShowdownPokemonRepository implements ShowdownPokemonRepositoryInterface
{
	/** @var PokemonId[] $pokemonToImport */
	private $pokemonToImport = [];

	/** @var ?PokemonId[] $pokemonToIgnore */
	private $pokemonToIgnore = [];

	/** @var string[] $unknownPokemon */
	private $unknownPokemon = [];

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
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->pokemonToImport[$result['name']] = new PokemonId($result['pokemon_id']);
		}

		$stmt = $db->prepare(
			'SELECT
				`name`,
				`pokemon_id`
			FROM `showdown_pokemon_to_ignore`'
		);
		$stmt->execute();
		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($result['pokemon_id'] !== null) {
				// The Pokémon Showdown Pokémon name has a Pokémon id.
				$pokemonId = new PokemonId($result['pokemon_id']);
			} else {
				$pokemonId = null;
			}

			$this->pokemonToIgnore[$result['name']] = $pokemonId;
		}
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
		// We use array_key_exists instead of isset because array_key_exists
		// returns true for null values, whereas isset would return false.
		return array_key_exists($showdownPokemonName, $this->pokemonToIgnore);
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
		return $this->isImported($showdownPokemonName)
			|| $this->isIgnored($showdownPokemonName)
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
	 * @throws PokemonNotImportedException if $showdownPokemonName is not an
	 *     imported Pokémon name.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId(string $showdownPokemonName) : PokemonId
	{
		// If the Pokémon is imported, return the Pokémon id.
		if ($this->isImported($showdownPokemonName)) {
			return $this->pokemonToImport[$showdownPokemonName];
		}

		// If the Pokémon is not known, add it to the list of unknown Pokémon.
		if (!$this->isKnown($showdownPokemonName)) {
			$this->addUnknown($showdownPokemonName);
		}

		throw new PokemonNotImportedException(
			'Pokémon should not be imported: ' . $showdownPokemonName
		);
	}

	/**
	 * Get the names of the unknown Pokémon the repository has tracked.
	 *
	 * @return string[]
	 */
	public function getUnknown() : array
	{
		return $this->unknownPokemon;
	}
}
