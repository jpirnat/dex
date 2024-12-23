<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedAbilityRepositoryInterface;

final readonly class AbilityModel
{
	public function __construct(
		private MovesetRatedAveragedAbilityRepositoryInterface $movesetRatedAveragedAbilityRepository,
		private AbilityNameRepositoryInterface $abilityNameRepository,
		private AbilityRepositoryInterface $abilityRepository,
	) {}

	/**
	 * Set individual Pokémon ability data averaged over multiple months.
	 */
	public function setData(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		// Get moveset rated averaged ability records for these months.
		$movesetRatedAveragedAbilities = $this->movesetRatedAveragedAbilityRepository->getByMonthsAndFormatAndRatingAndPokemon(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId,
		);

		$abilities = [];

		// Get each ability's data.
		foreach ($movesetRatedAveragedAbilities as $movesetRatedAveragedAbility) {
			$abilityId = $movesetRatedAveragedAbility->getAbilityId();

			// Get this ability's name.
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$abilityId,
			);

			// Get this ability.
			$ability = $this->abilityRepository->getById($abilityId);

			$abilities[] = [
				'identifier' => $ability->identifier,
				'name' => $abilityName->name,
				'percent' => $movesetRatedAveragedAbility->getPercent(),
			];
		}

		return $abilities;
	}
}
