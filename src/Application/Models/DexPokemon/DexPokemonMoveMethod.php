<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Closure;

final class DexPokemonMoveMethod
{
	private(set) string $identifier;
	private(set) string $name;
	private(set) string $description;

	/** @var DexPokemonMove[] $moves */
	private(set) array $moves;

	/**
	 * @param DexPokemonMove[] $moves
	 */
	public function __construct(
		string $identifier,
		string $name,
		string $description,
		array $moves,
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->description = $description;
		$this->moves = $moves;
	}

	public function sortMoves(Closure $sortBy) : void
	{
		uasort($this->moves, $sortBy);
	}
}
