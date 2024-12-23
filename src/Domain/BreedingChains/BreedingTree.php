<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\BreedingChains;

use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;

/**
 * This class represents a Pokémon breeding tree, as a node in a tree data
 * structure. The "root" node is the Pokémon ultimately being bred, and its
 * "child" nodes are the Pokémon's potential parents. Methods are named with
 * that domain-oriented reversal in mind (addParent and getParents instead of
 * addChild and getChildren).
 */
final class BreedingTree
{
	private PokemonMove $value;

	/** @var BreedingTree[] $parents */
	private array $parents = [];


	public function __construct(PokemonMove $value)
	{
		$this->value = $value;
	}


	/**
	 * Add a potential parent to the Pokémon.
	 */
	public function addParent(BreedingTree $parent) : void
	{
		$pokemonId = $parent->value->pokemonId->value();
		$this->parents[$pokemonId] = $parent;
	}

	/**
	 * Is this Pokémon already a potential parent?
	 */
	public function hasParent(int $pokemonId) : bool
	{
		return isset($this->parents[$pokemonId]);
	}

	/**
	 * Is this breeding chain complete? (Does it include an ancestor who learns
	 * the move by non-egg?)
	 */
	public function isComplete() : bool
	{
		if ($this->value->moveMethodId->value() !== MoveMethodId::EGG) {
			return true;
		}

		foreach ($this->parents as $parent) {
			if ($parent->isComplete()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get all potential breeding chains in this tree, in the form of an array
	 * of arrays of node values. For example, a tree with Ivysaur and Venusaur
	 * as the potential parents of Bulbasaur, and Chikorita as the only parent
	 * of Ivysaur, would return:
	 * [[Chikorita, Ivysaur, Bulbasaur], [Venusaur, Bulbasaur]]
	 *
	 * @return PokemonMove[][]
	 */
	public function getChains() : array
	{
		if ($this->parents === []) {
			return [[$this->value]];
		}

		$chains = [];

		foreach ($this->parents as $parent) {
			foreach ($parent->getChains() as $parentChain) {
				$parentChain[] = $this->value;

				$chains[] = $parentChain;
			}
		}

		return $chains;
	}
}
