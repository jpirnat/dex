<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionId;

final readonly class Evolution
{
	public function __construct(
		private VersionGroupId $versionGroupId,
		private FormId $evoFromId,
		private EvoMethodId $evoMethodId,
		private FormId $evoIntoId,
		private int $level,
		private ?ItemId $itemId,
		private ?MoveId $moveId,
		private ?PokemonId $pokemonId,
		private ?TypeId $typeId,
		private ?VersionId $versionId,
		private int $otherParameter,
	) {}

	public function getVersionGroupId() : VersionGroupId
	{
		return $this->versionGroupId;
	}

	public function getEvoFromId() : FormId
	{
		return $this->evoFromId;
	}

	public function getEvoMethodId() : EvoMethodId
	{
		return $this->evoMethodId;
	}

	public function getEvoIntoId() : FormId
	{
		return $this->evoIntoId;
	}

	public function getLevel() : int
	{
		return $this->level;
	}

	public function getItemId() : ?ItemId
	{
		return $this->itemId;
	}

	public function getMoveId() : ?MoveId
	{
		return $this->moveId;
	}

	public function getPokemonId() : ?PokemonId
	{
		return $this->pokemonId;
	}

	public function getTypeId() : ?TypeId
	{
		return $this->typeId;
	}

	public function getVersionId() : ?VersionId
	{
		return $this->versionId;
	}

	public function getOtherParameter() : int
	{
		return $this->otherParameter;
	}
}
