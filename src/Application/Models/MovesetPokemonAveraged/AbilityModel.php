<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonAveraged;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedAbilityRepositoryInterface;

class AbilityModel
{
	/** @var MovesetRatedAveragedAbilityRepositoryInterface $movesetRatedAveragedAbilityRepository */
	private $movesetRatedAveragedAbilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityData[] $abilityDatas */
	private $abilityDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedAveragedAbilityRepositoryInterface $movesetRatedAveragedAbilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 */
	public function __construct(
		MovesetRatedAveragedAbilityRepositoryInterface $movesetRatedAveragedAbilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		AbilityRepositoryInterface $abilityRepository
	) {
		$this->movesetRatedAveragedAbilityRepository = $movesetRatedAveragedAbilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->abilityRepository = $abilityRepository;
	}
	
	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated averaged ability records for these months.
		$movesetRatedAveragedAbilities = $this->movesetRatedAveragedAbilityRepository->getByMonthsAndFormatAndRatingAndPokemon(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each ability's data.
		foreach ($movesetRatedAveragedAbilities as $movesetRatedAveragedAbility) {
			$abilityId = $movesetRatedAveragedAbility->getAbilityId();

			// Get this ability's name.
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$abilityId
			);

			// Get this ability.
			$ability = $this->abilityRepository->getById($abilityId);

			$this->abilityDatas[] = new AbilityData(
				$abilityName->getName(),
				$ability->getIdentifier(),
				$movesetRatedAveragedAbility->getPercent()
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
