<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Trends;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Formats\FormatNameRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonAbilityRepositoryInterface;

class UsageAbilityTrendGenerator
{
	/** @var UsageRatedPokemonAbilityRepositoryInterface $usageRatedPokemonAbilityRepository */
	private $usageRatedPokemonAbilityRepository;

	/** @var FormatNameRepositoryInterface $formatNameRepository */
	private $formatNameRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonAbilityRepositoryInterface $usageRatedPokemonAbilityRepository
	 * @param FormatNameRepositoryInterface $formatNameRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 */
	public function __construct(
		UsageRatedPokemonAbilityRepositoryInterface $usageRatedPokemonAbilityRepository,
		FormatNameRepositoryInterface $formatNameRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		AbilityNameRepositoryInterface $abilityNameRepository
	) {
		$this->usageRatedPokemonAbilityRepository = $usageRatedPokemonAbilityRepository;
		$this->formatNameRepository = $formatNameRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->abilityNameRepository = $abilityNameRepository;
	}

	/**
	 * Get the data for a usage ability trend line.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return UsageAbilityTrendLine
	 */
	public function generate(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : UsageAbilityTrendLine {
		// Get the usage data.
		$usageRatedPokemonAbilities = $this->usageRatedPokemonAbilityRepository->getByFormatAndRatingAndPokemonAndAbility(
			$formatId,
			$rating,
			$pokemonId,
			$abilityId
		);

		// Get the name data.
		$formatName = $this->formatNameRepository->getByLanguageAndFormat($languageId, $formatId);
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon($languageId, $pokemonId);
		$abilityName = $this->abilityNameRepository->getByLanguageAndAbility($languageId, $abilityId);
	}
}
