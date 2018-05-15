<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
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

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityData[] $abilityDatas */
	private $abilityDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 */
	public function __construct(
		MovesetRatedAbilityRepositoryInterface $movesetRatedAbilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityRepositoryInterface $abilityRepository
	) {
		$this->movesetRatedAbilityRepository = $movesetRatedAbilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityRepository = $abilityRepository;
	}
	
	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param DateTime $thisMonth
	 * @param DateTime $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $thisMonth,
		DateTime $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated ability records for this month.
		$movesetRatedAbilities = $this->movesetRatedAbilityRepository->getByMonthAndFormatAndRatingAndPokemon(
			$thisMonth,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get moveset rated ability records for the previous month.
		$prevMonthAbilities = $this->movesetRatedAbilityRepository->getByMonthAndFormatAndRatingAndPokemon(
			$prevMonth,
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

			// Get this ability.
			$ability = $this->abilityRepository->getById($abilityId);

			// Get this ability's percent from the previous month.
			if (isset($prevMonthAbilities[$abilityId->value()])) {
				$change = $movesetRatedAbility->getPercent() - $prevMonthAbilities[$abilityId->value()]->getPercent();
			} else {
				$change = $movesetRatedAbility->getPercent();
			}

			$this->abilityDatas[] = new AbilityData(
				$abilityName->getName(),
				$ability->getIdentifier(),
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
