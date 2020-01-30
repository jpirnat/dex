<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Languages\LanguageId;

final class PokemonName
{
	private LanguageId $languageId;
	private PokemonId $pokemonId;
	private string $name;
	private string $category;

	/**
	 * Constructor.
	 *
	 * @param LanguageId $languageId
	 * @param PokemonId $pokemonId
	 * @param string $name
	 * @param string $category
	 */
	public function __construct(
		LanguageId $languageId,
		PokemonId $pokemonId,
		string $name,
		string $category
	) {
		$this->languageId = $languageId;
		$this->pokemonId = $pokemonId;
		$this->name = $name;
		$this->category = $category;
	}

	/**
	 * Get the Pokémon name's language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the Pokémon name's Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon name's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the Pokémon name's category.
	 *
	 * @return string
	 */
	public function getCategory() : string
	{
		return $this->category;
	}
}
