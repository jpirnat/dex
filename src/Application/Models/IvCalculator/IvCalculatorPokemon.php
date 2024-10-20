<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\IvCalculator;

use Jp\Dex\Domain\Types\DexType;

final readonly class IvCalculatorPokemon
{
	/**
	 * @param DexType[] $types
	 * @param int[] $baseStats
	 */
	public function __construct(
		private string $identifier,
		private string $name,
		private string $sprite,
		/** @var DexType[] $types */ private array $types,
		/** @var int[] $baseStats */ private array $baseStats,
		private int $bst,
	) {}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getSprite() : string
	{
		return $this->sprite;
	}

	/**
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * @return int[] Indexed by stat identifier.
	 */
	public function getBaseStats() : array
	{
		return $this->baseStats;
	}

	public function getBst() : int
	{
		return $this->bst;
	}
}
