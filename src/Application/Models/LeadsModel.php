<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\LeadsRatedPokemonRepositoryInterface;

class LeadsModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	protected $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	protected $pokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	protected $leadsRatedPokemonRepository;

	/** @var LeadsRatedPokemon[] $leadsRatedPokemon */
	protected $leadsRatedPokemon = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
	}


	/**
	 * Set the lead usage history of the requested Pokémon in the requested
	 * format across all ratings.
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

		$this->leadsRatedPokemon = $this->leadsRatedPokemonRepository->getByFormatAndPokemon(
			$format->id(),
			$pokemon->id()
		);
	}

	/**
	 * Get the lead usage history of the requested Pokémon in the requested
	 * format across all ratings.
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getUsage() : array
	{
		return $this->leadsRatedPokemon;
	}
}
