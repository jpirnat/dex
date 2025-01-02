<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Abilities\AbilityTypeMatchups;
use Jp\Dex\Domain\Abilities\ExpandedDexPokemonAbility;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroup;

final class DexPokemonMatchupsModel
{
	/** @var DexType[] $types */
	private(set) array $types = [];

	/** @var float[][] $damageTaken */
	private(set) array $damageTaken = [];

	private(set) array $abilities = [];


	private const string NO_ABILITY = 'none';


	public function __construct(
		private readonly DexTypeRepositoryInterface $dexTypeRepository,
		private readonly VgPokemonRepositoryInterface $vgPokemonRepository,
		private readonly TypeMatchupRepositoryInterface $typeMatchupRepository,
		private readonly AbilityTypeMatchups $abilityTypeMatchups,
	) {}


	/**
	 * Set data for the dex Pokémon page's matchups.
	 *
	 * @param ExpandedDexPokemonAbility[] $abilities
	 */
	public function setData(
		VersionGroup $versionGroup,
		PokemonId $pokemonId,
		LanguageId $languageId,
		array $abilities,
	) : void {
		$this->damageTaken = [];
		$this->abilities = [];

		// Get all types, and initialize their matchup multipliers to 1.
		$allTypes = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroup->id,
			$languageId,
		);
		foreach ($allTypes as $type) {
			$identifier = $type->identifier;
			$this->damageTaken[self::NO_ABILITY][$identifier] = 1;
		}

		// Get the Pokémon's types, then get the matchups for those types.
		$vgPokemon = $this->vgPokemonRepository->getByVgAndPokemon(
			$versionGroup->id,
			$pokemonId,
		);
		foreach ($vgPokemon->getTypeIds() as $typeId) {
			$matchups = $this->typeMatchupRepository->getByDefendingType(
				$versionGroup->generationId,
				$typeId,
			);
			foreach ($matchups as $matchup) {
				// Factor this matchup into the Pokémon's overall matchups.
				$attackingTypeIdentifier = $matchup->attackingTypeIdentifier;
				$multiplier = $matchup->multiplier;

				$this->damageTaken[self::NO_ABILITY][$attackingTypeIdentifier] *= $multiplier;
			}
		}

		if ($versionGroup->hasAbilities) {
			foreach ($abilities as $ability) {
				$hasMatchups = $this->abilityTypeMatchups->hasMatchups(
					$versionGroup->generationId,
					$ability->identifier,
				);

				if ($hasMatchups) {
					$this->abilities[] = [
						'identifier' => $ability->identifier,
						'name' => $ability->name,
					];

					$abilityMultipliers = $this->abilityTypeMatchups->getMatchups(
						$versionGroup->generationId,
						$ability->identifier,
						$this->damageTaken[self::NO_ABILITY],
					);

					$abilityIdentifier = $ability->identifier;
					$this->damageTaken[$abilityIdentifier] = $abilityMultipliers;
				}
			}
		}

		$this->abilities[] = [
			'identifier' => self::NO_ABILITY,
			'name' => 'Other Ability',
		];

		$this->types = $allTypes;
	}
}
