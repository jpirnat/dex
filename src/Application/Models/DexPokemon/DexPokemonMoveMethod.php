<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

final class DexPokemonMoveMethod
{
	private string $identifier;
	private string $name;
	private string $description;

	/** @var DexPokemonMove[] $moves */
	private array $moves;

	/**
	 * Constructor.
	 *
	 * @param string $identifier
	 * @param string $name
	 * @param string $description
	 * @param DexPokemonMove[] $moves
	 */
	public function __construct(
		string $identifier,
		string $name,
		string $description,
		array $moves
	) {
		$this->identifier = $identifier;
		$this->name = $name;
		$this->description = $description;
		$this->moves = $moves;
	}

	/**
	 * Get the method's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the method's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the method's description.
	 *
	 * @return string
	 */
	public function getDescription() : string
	{
		return $this->description;
	}

	/**
	 * Get the method's moves.
	 *
	 * @return DexPokemonMove[] Returned as a reference so it can be used with
	 *     uasort.
	 */
	public function &getMoves() : array
	{
		return $this->moves;
	}
}
