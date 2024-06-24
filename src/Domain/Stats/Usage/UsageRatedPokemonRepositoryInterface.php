<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;

interface UsageRatedPokemonRepositoryInterface
{
	/**
	 * Do any usage rated Pokémon records exist for this month, format, and rating?
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool;

	/**
	 * Save a usage rated Pokémon record.
	 */
	public function save(UsageRatedPokemon $usageRatedPokemon) : void;

	/**
	 * Get the usage rated Pokémon id for this month, format, rating, and Pokémon.
	 */
	public function getId(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
	) : ?UsageRatedPokemonId;
}
