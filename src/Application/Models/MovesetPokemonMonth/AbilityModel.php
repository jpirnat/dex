<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use Jp\Dex\Domain\YearMonth;

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
	 * @param YearMonth $thisMonth
	 * @param YearMonth $lastMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		YearMonth $thisMonth,
		YearMonth $lastMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated ability records for this month.
		$movesetRatedAbilities = $this->movesetRatedAbilityRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$formatId,
			$rating,
			$pokemonId
		);

		// Get moveset rated ability records for last month.
		$lastMonthAbilities = $this->movesetRatedAbilityRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$lastMonth->getYear(),
			$lastMonth->getMonth(),
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each ability's data.
		foreach ($movesetRatedAbilities as $movesetRatedAbility) {
			$abilityId = $movesetRatedAbility->getAbilityId();

			// Get this ability's name.
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$abilityId
			);

			// Get this ability's percent from last month.
			if (isset($lastMonthAbilities[$abilityId->value()])) {
				$change = $movesetRatedAbility->getPercent() - $lastMonthAbilities[$abilityId->value()]->getPercent();
			} else {
				$change = $movesetRatedAbility->getPercent();
			}

			$this->abilityDatas[] = new AbilityData(
				$abilityName->getName(),
				$movesetRatedAbility->getPercent(),
				$change
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
