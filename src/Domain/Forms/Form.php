<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Forms;

use Jp\Dex\Domain\Pokemon\PokemonId;

final readonly class Form
{
	public function __construct(
		private FormId $id,
		private string $identifier,
		private PokemonId $pokemonId,
	) {}

	public function getId() : FormId
	{
		return $this->id;
	}

	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}
}
