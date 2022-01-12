<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Types\DexType;

final class DexPokemonMove
{
	public function __construct(
		private array $versionGroupData,
		private string $identifier,
		private string $name,
		private DexType $type,
		private DexCategory $category,
		private int $pp,
		private int $power,
		private int $accuracy,
		private string $description,
	) {}

	/**
	 * Get the version group data.
	 */
	public function getVersionGroupData() : array
	{
		return $this->versionGroupData;
	}

	/**
	 * Get the move's identifier.
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the move's name.
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Get the move's type.
	 */
	public function getType() : DexType
	{
		return $this->type;
	}

	/**
	 * Get the move's category.
	 */
	public function getCategory() : DexCategory
	{
		return $this->category;
	}

	/**
	 * Get the move's PP.
	 */
	public function getPP() : int
	{
		return $this->pp;
	}

	/**
	 * Get the move's power.
	 */
	public function getPower() : int
	{
		return $this->power;
	}

	/**
	 * Get the move's accuracy.
	 */
	public function getAccuracy() : int
	{
		return $this->accuracy;
	}

	/**
	 * Get the move's description.
	 */
	public function getDescription() : string
	{
		return $this->description;
	}
}
