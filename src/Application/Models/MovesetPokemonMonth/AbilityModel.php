<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;

class AbilityModel
{
	/** @var MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository */
	private $movesetRatedAbilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityData[] $abilityDatas */
	private $abilityDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 */
	public function __construct(
		MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository
	) {
		$this->movesetRatedAbilityRepository = $movesetRatedAbilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
	}
	
	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated ability records.
		$movesetRatedAbilities = $this->movesetRatedAbilityRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each ability's data.
		foreach ($movesetRatedAbilities as $movesetRatedAbility) {
			// Get this ability's name.
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$movesetRatedAbility->getAbilityId()
			);

			$this->abilityDatas[] = new AbilityData(
				$abilityName->getName(),
				$movesetRatedAbility->getPercent()
			);
		}
	}

	/**
	 * Get the ability datas.
	 *
	 * @return AbilityData[]
	 */
	public function getAbilityDatas() : array
	{
		return $this->abilityDatas;
	}
}
