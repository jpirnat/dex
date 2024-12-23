<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Forms;

use Jp\Dex\Domain\Pokemon\PokemonId;

final readonly class Form
{
	public function __construct(
		private(set) FormId $id,
		private(set) string $identifier,
		private(set) PokemonId $pokemonId,
	) {}
}
