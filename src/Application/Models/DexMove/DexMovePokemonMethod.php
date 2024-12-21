<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexMove;

use Closure;

final class DexMovePokemonMethod
{
	private(set) string $identifier;
	private(set) string $name;
	private(set) string $description;

	/** @var DexMovePokemon[] $pokemon */
	private(set) array $pokemon;


	/**
	 * @param DexMovePokemon[] $pokemon
	 */
	public function __construct(
		string $identifier,
		string $name,
		string $description,
		array $pokemon,
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->description = $description;
		$this->pokemon = $pokemon;
	}

	public function sortPokemon(Closure $sortBy) : void
	{
		uasort($this->pokemon, $sortBy);
	}
}
