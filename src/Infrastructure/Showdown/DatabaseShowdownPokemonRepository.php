<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure\Showdown;

use Jp\Dex\Domain\Import\Showdown\PokemonNotImportedException;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use PDO;

final class DatabaseShowdownPokemonRepository implements ShowdownPokemonRepositoryInterface
{
	/** @var PokemonId[] $pokemonToImport */
	private array $pokemonToImport = [];

	/** @var array<string, int> $pokemonToIgnore */
	private array $pokemonToIgnore = [];

	/** @var string[] $unknownPokemon */
	private array $unknownPokemon = [];


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
				1
			FROM `showdown_pokemon_to_ignore`'
		);
		$stmt->execute();
		$this->pokemonToIgnore = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
	 * Is the Pokémon Showdown Pokémon name known and imported?
	 */
	public function isImported(string $showdownPokemonName) : bool
	{
		return isset($this->pokemonToImport[$showdownPokemonName]);
	}

	/**
	 * Is the Pokémon Showdown Pokémon name known and ignored?
	 */
	public function isIgnored(string $showdownPokemonName) : bool
	{
		return isset($this->pokemonToIgnore[$showdownPokemonName]);
	}

	/**
	 * Is the Pokémon Showdown Pokémon name known?
	 */
	public function isKnown(string $showdownPokemonName) : bool
	{
		return $this->isImported($showdownPokemonName)
			|| $this->isIgnored($showdownPokemonName)
		;
	}

	/**
	 * Add a Pokémon Showdown Pokémon name to the list of unknown Pokémon.
	 */
	public function addUnknown(string $showdownPokemonName) : void
	{
		$this->unknownPokemon[$showdownPokemonName] = $showdownPokemonName;
	}

	/**
	 * Get the Pokémon id of a Pokémon Showdown Pokemon name.
	 *
	 * @throws PokemonNotImportedException if $showdownPokemonName is not an
	 *     imported Pokémon name.
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
