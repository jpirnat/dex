<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class PokemonName
{
	public function __construct(
		private LanguageId $languageId,
		private PokemonId $pokemonId,
		private string $name,
		private string $category,
	) {}

	/**
	 * Get the Pokémon name's language id.
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the Pokémon name's Pokémon id.
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the Pokémon name's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the Pokémon name's category.
	 */
	public function getCategory() : string
	{
		return $this->category;
	}
}
