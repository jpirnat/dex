<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroup;

final class DexPokemonMatchupsModel
{
	/** @var DexType[] $types */
	private array $types = [];

	/** @var float[][] $damageTaken */
	private array $damageTaken = [];

	private array $abilities = [];


	private const NO_ABILITY = 'none';


	public function __construct(
		private readonly DexTypeRepositoryInterface $dexTypeRepository,
		private readonly TypeMatchupRepositoryInterface $typeMatchupRepository,
	) {}


	/**
	 * Set data for the dex Pokémon page's matchups.
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
			$versionGroup->getId(),
			$languageId,
		);
		foreach ($allTypes as $type) {
			$identifier = $type->getIdentifier();
			$this->damageTaken[self::NO_ABILITY][$identifier] = 1;
		}

		// Get the Pokémon's types, then get the matchups for those types.
		$pokemonTypes = $this->dexTypeRepository->getByPokemon(
			$versionGroup->getId(),
			$pokemonId,
			$languageId,
		);
		foreach ($pokemonTypes as $type) {
			$matchups = $this->typeMatchupRepository->getByDefendingType(
				$versionGroup->getGenerationId(),
				$type->getId(),
			);
			foreach ($matchups as $matchup) {
				// Factor this matchup into the Pokémon's overall matchups.
				$attackingTypeIdentifier = $matchup->getAttackingTypeIdentifier();
				$multiplier = $matchup->getMultiplier();

				$this->damageTaken[self::NO_ABILITY][$attackingTypeIdentifier] *= $multiplier;
			}
		}

		foreach ($abilities as $ability) {
			$this->checkForMatchups($versionGroup->getGenerationId(), $ability);
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
	private function checkForMatchups(GenerationId $generationId, array $ability) : void
	{
		$abilityId = $ability['id'];
		$identifier = $ability['identifier'];

		// TODO: Get the type identifiers from somewhere.

		if ($abilityId === AbilityId::VOLT_ABSORB) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['electric'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::WATER_ABSORB) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['water'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::FLASH_FIRE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::WONDER_GUARD) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$identifier] as $type => $multiplier) {
				if ($multiplier <= 1) {
					$this->damageTaken[$identifier][$type] *= 0;
				}
			}
			return;
		}

		if ($abilityId === AbilityId::LEVITATE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['ground'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::LIGHTNING_ROD && $generationId->value() >= 5) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['electric'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::THICK_FAT) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= .5;
			$this->damageTaken[$identifier]['ice'] *= .5;
			return;
		}

		if ($abilityId === AbilityId::MOTOR_DRIVE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['electric'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::HEATPROOF) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= .5;
			return;
		}

		if ($abilityId === AbilityId::DRY_SKIN) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= 1.25;
			$this->damageTaken[$identifier]['water'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::FILTER) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$identifier] as $type => $multiplier) {
				if ($multiplier > 1) {
					$this->damageTaken[$identifier][$type] *= .75;
				}
			}
			return;
		}

		if ($abilityId === AbilityId::STORM_DRAIN && $generationId->value() >= 5) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['water'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::SOLID_ROCK) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$identifier] as $type => $multiplier) {
				if ($multiplier > 1) {
					$this->damageTaken[$identifier][$type] *= .75;
				}
			}
			return;
		}

		if ($abilityId === AbilityId::SAP_SIPPER) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['grass'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::WATER_BUBBLE) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= .5;
			return;
		}

		if ($abilityId === AbilityId::FLUFFY) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= 2;
			return;
		}

		if ($abilityId === AbilityId::PRISM_ARMOR) {
			$this->addToDamageTaken($ability);
			foreach ($this->damageTaken[$identifier] as $type => $multiplier) {
				if ($multiplier > 1) {
					$this->damageTaken[$identifier][$type] *= .75;
				}
			}
			return;
		}

		if ($abilityId === AbilityId::PURIFYING_SALT) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['ghost'] *= .5;
			return;
		}

		if ($abilityId === AbilityId::WELL_BAKED_BODY) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['fire'] *= 0;
			return;
		}

		if ($abilityId === AbilityId::EARTH_EATER) {
			$this->addToDamageTaken($ability);
			$this->damageTaken[$identifier]['ground'] *= 0;
		}
	}


	/**
	 * Add this ability to the matchups array.
	 */
	private function addToDamageTaken(array $ability) : void
	{
		$this->abilities[] = [
			'identifier' => $ability['identifier'],
			'name' => $ability['name'],
		];

		$this->damageTaken[$ability['identifier']] = $this->damageTaken[self::NO_ABILITY];
	}


	/**
	 * Get the types.
	 *
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * Get the damage dealt multipliers for each type, for each ability that has
	 * unique matchups.
	 *
	 * @return float[][]
	 */
	public function getDamageTaken() : array
	{
		return $this->damageTaken;
	}

	/**
	 * Get the abilities that have unique matchups.
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}
}
