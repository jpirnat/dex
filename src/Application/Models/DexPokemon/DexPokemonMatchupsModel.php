<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Abilities\AbilityIdentifier;
use Jp\Dex\Domain\Abilities\ExpandedDexPokemonAbility;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\VgPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeIdentifier;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
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
				$this->checkForMatchups($versionGroup->generationId, $ability);
			}
		}

		$this->abilities[] = [
			'identifier' => self::NO_ABILITY,
			'name' => 'Other Ability',
		];

		$this->types = $allTypes;
	}

	/**
	 * If this ability changes any of the Pokémon's matchups, add the ability
	 * to the matchups array.
	 */
	private function checkForMatchups(GenerationId $generationId, ExpandedDexPokemonAbility $ability) : void
	{
		$abilityIdentifier = $ability->identifier;

		if ($abilityIdentifier === AbilityIdentifier::VOLT_ABSORB) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::ELECTRIC] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::WATER_ABSORB) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::WATER] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::FLASH_FIRE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::WONDER_GUARD) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$abilityIdentifier] as $type => $multiplier) {
				if ($multiplier <= 1) {
					$this->damageTaken[$abilityIdentifier][$type] *= 0;
				}
			}
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::LEVITATE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::GROUND] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::LIGHTNING_ROD && $generationId->value() >= 5) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::ELECTRIC] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::THICK_FAT) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= .5;
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::ICE] *= .5;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::MOTOR_DRIVE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::ELECTRIC] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::HEATPROOF) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= .5;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::DRY_SKIN) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= 1.25;
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::WATER] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::FILTER) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$abilityIdentifier] as $type => $multiplier) {
				if ($multiplier > 1) {
					$this->damageTaken[$abilityIdentifier][$type] *= .75;
				}
			}
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::STORM_DRAIN && $generationId->value() >= 5) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::WATER] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::SOLID_ROCK) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$abilityIdentifier] as $type => $multiplier) {
				if ($multiplier > 1) {
					$this->damageTaken[$abilityIdentifier][$type] *= .75;
				}
			}
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::SAP_SIPPER) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::GRASS] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::WATER_BUBBLE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= .5;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::FLUFFY) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= 2;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::PRISM_ARMOR) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$abilityIdentifier] as $type => $multiplier) {
				if ($multiplier > 1) {
					$this->damageTaken[$abilityIdentifier][$type] *= .75;
				}
			}
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::PURIFYING_SALT) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::GHOST] *= .5;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::WELL_BAKED_BODY) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::FIRE] *= 0;
			return;
		}

		if ($abilityIdentifier === AbilityIdentifier::EARTH_EATER) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$abilityIdentifier][TypeIdentifier::GROUND] *= 0;
		}
	}


	/**
	 * Add this ability to the matchups array.
	 */
	private function addToDamageTaken(ExpandedDexPokemonAbility $ability) : void
	{
		$this->abilities[] = [
			'identifier' => $ability->identifier,
			'name' => $ability->name,
		];

		$abilityIdentifier = $ability->identifier;
		$this->damageTaken[$abilityIdentifier] = $this->damageTaken[self::NO_ABILITY];
	}
}
