<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Languages\LanguageId;

final readonly class PokemonName
{
	public function __construct(
		private(set) LanguageId $languageId,
		private(set) PokemonId $pokemonId,
		private(set) string $name,
		private(set) string $category,
	) {}
}
