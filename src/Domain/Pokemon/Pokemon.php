<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Pokemon;

use Jp\Dex\Domain\Species\SpeciesId;

final readonly class Pokemon
{
	public function __construct(
		private(set) PokemonId $id,
		private(set) string $identifier,
		private(set) ?string $pokemonIdentifier,
		private(set) SpeciesId $speciesId,
		private(set) bool $isDefaultPokemon,
		private(set) ExperienceGroupId $experienceGroupId,
		private(set) int $genderRatio,
		private(set) string $smogonDexIdentifier,
		private(set) int $sort,
	) {}
}
