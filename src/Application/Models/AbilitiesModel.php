<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\MovesetRatedAbilityRepositoryInterface;

class AbilitiesModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	protected $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	protected $pokemonRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	protected $abilityRepository;

	/** @var MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository */
	protected $movesetRatedAbilityRepository;

	/** @var MovesetRatedAbility[] $movesetRatedAbilities */
	protected $movesetRatedAbilities = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		AbilityRepositoryInterface $abilityRepository,
		MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->abilityRepository = $abilityRepository;
		$this->movesetRatedAbilityRepository = $movesetRatedAbilityRepository;
	}


	/**
	 * Set the ability usage history of the requested Pokémon in the requested
	 * format for the requested rating.
	 *
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $pokemonIdentifier
	 *
	 * @return void
	 */
	public function setRatingUsage(
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier
	) : void {
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		$this->movesetRatedAbilities = $this->movesetRatedAbilityRepository->getByFormatAndRatingAndPokemon(
			$format->id(),
			$rating,
			$pokemon->id()
		);
	}

	/**
	 * Set the ability usage history of the requested Pokémon in the requested
	 * format for the requested ability across all ratings.
	 *
	 * @param string $formatIdentifier
	 * @param string $pokemonIdentifier
	 * @param string $abilityIdentifier
	 *
	 * @return void
	 */
	public function setAbilityUsage(
		string $formatIdentifier,
		string $pokemonIdentifier,
		string $abilityIdentifier
	) : void {
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);

		$this->movesetRatedAbilities = $this->movesetRatedAbilityRepository->getByFormatAndPokemonAndAbility(
			$format->id(),
			$pokemon->id(),
			$ability->id()
		);
	}

	/**
	 * Get the ability usage history.
	 *
	 * @return MovesetRatedAbility[]
	 */
	public function getUsage() : array
	{
		return $this->movesetRatedAbilities;
	}
}
