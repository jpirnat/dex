<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Abilities;

use Jp\Dex\Domain\Types\TypeIdentifier;
use Jp\Dex\Domain\Versions\GenerationId;

final readonly class AbilityTypeMatchups
{
	public function hasMatchups(
		GenerationId $generationId,
		string $abilityIdentifier,
	) : bool {
		// Abilities with matchups in every generation.
		if (in_array($abilityIdentifier, [
			AbilityIdentifier::VOLT_ABSORB,
			AbilityIdentifier::WATER_ABSORB,
			AbilityIdentifier::FLASH_FIRE,
			AbilityIdentifier::WONDER_GUARD,
			AbilityIdentifier::LEVITATE,
			AbilityIdentifier::THICK_FAT,
			AbilityIdentifier::MOTOR_DRIVE,
			AbilityIdentifier::HEATPROOF,
			AbilityIdentifier::DRY_SKIN,
			AbilityIdentifier::FILTER,
			AbilityIdentifier::SOLID_ROCK,
			AbilityIdentifier::SAP_SIPPER,
			AbilityIdentifier::WATER_BUBBLE,
			AbilityIdentifier::FLUFFY,
			AbilityIdentifier::PRISM_ARMOR,
			AbilityIdentifier::PURIFYING_SALT,
			AbilityIdentifier::WELL_BAKED_BODY,
			AbilityIdentifier::EARTH_EATER,
		], true)) {
			return true;
		}

		// Abilities with matchups in only some generations.
		if ($abilityIdentifier === AbilityIdentifier::LIGHTNING_ROD && $generationId->value >= 5) {
			return true;
		}
		if ($abilityIdentifier === AbilityIdentifier::STORM_DRAIN && $generationId->value >= 5) {
			return true;
		}

		return false;
	}

	/**
	 * @var float[] $multipliers Indexed by attacking type id. These are the
	 *     multipliers before the ability is applied.
	 *
	 * @return float[]
	 */
	public function getMatchups(
		GenerationId $generationId,
		string $abilityIdentifier,
		array $multipliers,
	) : array {
		if ($abilityIdentifier === AbilityIdentifier::VOLT_ABSORB) {
			$multipliers[TypeIdentifier::ELECTRIC] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::WATER_ABSORB) {
			$multipliers[TypeIdentifier::WATER] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::FLASH_FIRE) {
			$multipliers[TypeIdentifier::FIRE] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::WONDER_GUARD) {
			foreach ($multipliers as $attackingTypeIdentifier => $multiplier) {
				if ($multiplier <= 1) {
					$multipliers[$attackingTypeIdentifier] *= 0;
				}
			}
		}

		if ($abilityIdentifier === AbilityIdentifier::LEVITATE) {
			$multipliers[TypeIdentifier::GROUND] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::LIGHTNING_ROD && $generationId->value >= 5) {
			$multipliers[TypeIdentifier::ELECTRIC] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::THICK_FAT) {
			$multipliers[TypeIdentifier::FIRE] *= .5;
			$multipliers[TypeIdentifier::ICE] *= .5;
		}

		if ($abilityIdentifier === AbilityIdentifier::MOTOR_DRIVE) {
			$multipliers[TypeIdentifier::ELECTRIC] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::HEATPROOF) {
			$multipliers[TypeIdentifier::FIRE] *= .5;
		}

		if ($abilityIdentifier === AbilityIdentifier::DRY_SKIN) {
			$multipliers[TypeIdentifier::FIRE] *= 1.25;
			$multipliers[TypeIdentifier::WATER] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::FILTER) {
			foreach ($multipliers as $attackingTypeIdentifier => $multiplier) {
				if ($multiplier > 1) {
					$multipliers[$attackingTypeIdentifier] *= .75;
				}
			}
		}

		if ($abilityIdentifier === AbilityIdentifier::STORM_DRAIN && $generationId->value >= 5) {
			$multipliers[TypeIdentifier::WATER] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::SOLID_ROCK) {
			foreach ($multipliers as $attackingTypeIdentifier => $multiplier) {
				if ($multiplier > 1) {
					$multipliers[$attackingTypeIdentifier] *= .75;
				}
			}
		}

		if ($abilityIdentifier === AbilityIdentifier::SAP_SIPPER) {
			$multipliers[TypeIdentifier::GRASS] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::WATER_BUBBLE) {
			$multipliers[TypeIdentifier::FIRE] *= .5;
		}

		if ($abilityIdentifier === AbilityIdentifier::FLUFFY) {
			$multipliers[TypeIdentifier::FIRE] *= 2;
		}

		if ($abilityIdentifier === AbilityIdentifier::PRISM_ARMOR) {
			foreach ($multipliers as $attackingTypeIdentifier => $multiplier) {
				if ($multiplier > 1) {
					$multipliers[$attackingTypeIdentifier] *= .75;
				}
			}
		}

		if ($abilityIdentifier === AbilityIdentifier::PURIFYING_SALT) {
			$multipliers[TypeIdentifier::GHOST] *= .5;
		}

		if ($abilityIdentifier === AbilityIdentifier::WELL_BAKED_BODY) {
			$multipliers[TypeIdentifier::FIRE] *= 0;
		}

		if ($abilityIdentifier === AbilityIdentifier::EARTH_EATER) {
			$multipliers[TypeIdentifier::GROUND] *= 0;
		}

		return $multipliers;
	}
}
