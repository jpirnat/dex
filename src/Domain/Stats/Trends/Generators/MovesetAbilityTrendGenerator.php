<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Generators;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Lines\MovesetAbilityTrendLine;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class MovesetAbilityTrendGenerator
{
	/** @var MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository */
	private $movesetRatedAbilityRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var PokemonTypeRepositoryInterface $pokemonTypeRepository */
	private $pokemonTypeRepository;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TrendPointCalculator $trendPointCalculator */
	private $trendPointCalculator;

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TrendPointCalculator $trendPointCalculator
	 */
	public function __construct(
		MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		TrendPointCalculator $trendPointCalculator
	) {
		$this->movesetRatedAbilityRepository = $movesetRatedAbilityRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->trendPointCalculator = $trendPointCalculator;
	}

	/**
	 * Get the data for a moveset ability trend line.
	 *
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return MovesetAbilityTrendLine
	 */
	public function generate(
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : MovesetAbilityTrendLine {
		// Get the name data.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);
		$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$abilityId
		);

		// Get the Pokémon's primary type.
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
			$format->getGenerationId(),
			$pokemonId
		);
		$pokemonType = $this->typeRepository->getById($pokemonTypes[1]->getTypeId());

		// Get the usage data.
		$movesetRatedAbilities = $this->movesetRatedAbilityRepository->getByFormatAndRatingAndPokemonAndAbility(
			$format->getId(),
			$rating,
			$pokemonId,
			$abilityId
		);

		// Get the trend points.
		$trendPoints = $this->trendPointCalculator->getTrendPoints(
			$format->getId(),
			$movesetRatedAbilities,
			'getPercent',
			0
		);

		return new MovesetAbilityTrendLine(
			$format->getName(),
			$rating,
			$pokemonName,
			$abilityName,
			$pokemonType,
			$trendPoints
		);
	}
}
