<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;

class UsageModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	protected $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	protected $pokemonRepository;

	/** @var UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository */
	protected $usageRatedPokemonRepository;

	/** @var UsageRatedPokemon[] $usageRatedPokemon */
	protected $usageRatedPokemon = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
	}

	/**
	 * Set the usage history of the requested Pokémon in the requested format
	 * across all ratings.
	 *
	 * @param string $formatIdentifier
	 * @param string $pokemonIdentifier
	 *
	 * @return void
	 */
	public function setUsage(string $formatIdentifier, string $pokemonIdentifier) : void
	{
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		$this->usageRatedPokemon = $this->usageRatedPokemonRepository->getByFormatAndPokemon(
			$format->id(),
			$pokemon->id()
		);
	}

	/**
	 * Get the usage history of the requested Pokémon in the requested format
	 * across all ratings.
	 *
	 * @return UsageRatedPokemon[]
	 */
	public function getUsage() : array
	{
		return $this->usageRatedPokemon;
	}
}
