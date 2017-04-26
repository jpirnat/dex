<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Languages\LanguageId;

class PokemonName
{
	/** @var LanguageId $languageId */
	protected $languageId;

	/** @var PokemonId $pokemonId */
	protected $pokemonId;

	/** @var string $name */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param PokemonId $pokemonId
	 * @param string $name
	 */
	public function __construct(
		LanguageId $languageId,
		PokemonId $pokemonId,
		string $name
	) {
		$this->languageId = $languageId;
		$this->pokemonId = $pokemonId;
		$this->name = $name;
	}

	/**
	 * Get the Pokémon name's language id.
	 *
	 * @return LanguageId
	 */
	public function languageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the Pokémon name's Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function pokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon name's name value.
	 *
	 * @return string
	 */
	public function name() : string
	{
		return $this->name;
	}
}
