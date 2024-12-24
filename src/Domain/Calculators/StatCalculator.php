<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

use Exception;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Versions\GenerationId;

final readonly class StatCalculator
{
	private const int PERFECT_IV_GEN_1 = 15;
	private const int PERFECT_IV_GEN_3 = 31;

	/**
	 * Calculate a Pokémon's HP stat in generations 1 or 2.
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Generations_I_and_II
	 */
	public function gen1Hp(int $base, int $dv, int $statexp, int $level) : int
	{
		return (int) ((($base + $dv) * 2 + (int) (ceil(sqrt($statexp)) / 4)) * $level / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 1 or 2.
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Generations_I_and_II
	 */
	public function gen1Other(int $base, int $dv, int $statexp, int $level) : int
	{
		return (int) ((($base + $dv) * 2 + (int) (ceil(sqrt($statexp)) / 4)) * $level / 100) + 5;
	}

	/**
	 * Calculate a Pokémon's stats in generations 1 or 2.
	 */
	public function all1(
		GenerationId $generationId,
		StatValueContainer $baseStats,
		StatValueContainer $ivSpread,
		StatValueContainer $evSpread,
		int $level,
	) : StatValueContainer {
		$statSpread = new StatValueContainer();

		$statIds = StatId::getByGeneration($generationId);
		foreach ($statIds as $statId) {
			if ($statId->value === StatId::HP) {
				// Calculate HP.
				$value = $this->gen1Hp(
					(int) $baseStats->get($statId)->value,
					(int) $ivSpread->get($statId)->value,
					(int) $evSpread->get($statId)->value,
					$level,
				);
				$statSpread->add(new StatValue($statId, $value));
				continue;
			}

			$value = $this->gen1Other(
				(int) $baseStats->get($statId)->value,
				(int) $ivSpread->get($statId)->value,
				(int) $evSpread->get($statId)->value,
				$level,
			);
			$statSpread->add(new StatValue($statId, $value));
		}

		return $statSpread;
	}

	/**
	 * Calculate one of a Pokémon's stats in generations 3 and above.
	 */
	public function gen3Stat(StatId $statId, int $base, int $iv, int $ev, int $level, float $natureModifier) : int
	{
		return match ($statId->value) {
			StatId::HP => $this->gen3Hp($base, $iv, $ev, $level),
			default => $this->gen3Other($base, $iv, $ev, $level, $natureModifier),
		};
	}

	/**
	 * Calculate a Pokémon's HP stat in generations 3 and above.
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Generation_III_onward
	 */
	public function gen3Hp(int $base, int $iv, int $ev, int $level) : int
	{
		// Shedinja hack.
		if ($base === 1) {
			return 1;
		}

		return (int) ((2 * $base + $iv + (int) ($ev / 4)) * $level / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 3 and above.
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Generation_III_onward
	 */
	public function gen3Other(int $base, int $iv, int $ev, int $level, float $natureModifier) : int
	{
		return (int) (((int) ((2 * $base + $iv + (int) ($ev / 4)) * $level / 100) + 5) * $natureModifier);
	}

	/**
	 * Calculate a Pokémon's stats in generations 3 and above.
	 */
	public function all3(
		StatValueContainer $baseStats,
		StatValueContainer $ivSpread,
		StatValueContainer $evSpread,
		int $level,
		?StatId $increasedStatId,
		?StatId $decreasedStatId,
	) : StatValueContainer {
		$statSpread = new StatValueContainer();

		$statIds = StatId::getByGeneration(new GenerationId(3));
		foreach ($statIds as $statId) {
			if ($statId->value === StatId::HP) {
				// Calculate HP.
				$value = $this->gen3Hp(
					(int) $baseStats->get($statId)->value,
					(int) $ivSpread->get($statId)->value,
					(int) $evSpread->get($statId)->value,
					$level,
				);
				$statSpread->add(new StatValue($statId, $value));
				continue;
			}

			$value = $this->gen3Other(
				(int) $baseStats->get($statId)->value,
				(int) $ivSpread->get($statId)->value,
				(int) $evSpread->get($statId)->value,
				$level,
				$this->getNatureModifier($statId, $increasedStatId, $decreasedStatId),
			);
			$statSpread->add(new StatValue($statId, $value));
		}

		return $statSpread;
	}

	/**
	 * Get the nature modifier for this stat.
	 */
	public function getNatureModifier(StatId $statId, ?StatId $increasedStatId, ?StatId $decreasedStatId) : float
	{
		if ($increasedStatId === null) {
			return 1;
		}

		if ($statId->value === $increasedStatId->value) {
			return 1.1;
		}

		if ($statId->value === $decreasedStatId->value) {
			return 0.9;
		}

		return 1;
	}

	/**
	 * Calculate a Pokémon's HP stat in Let's Go, Pikachu! or Let's Go, Eevee!
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Pok%C3%A9mon:_Let's_Go,_Pikachu!_and_Let's_Go,_Eevee!
	 */
	public function letsGoHp(int $base, int $iv, int $av, int $level) : int
	{
		return (int) ((2 * $base + $iv) * $level / 100) + $level + 10 + $av;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in Let's Go, Pikachu! or Let's Go, Eevee!
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Pok%C3%A9mon:_Let's_Go,_Pikachu!_and_Let's_Go,_Eevee!
	 */
	public function letsGoOther(int $base, int $iv, int $av, int $level, float $natureModifier, float $friendshipModifier) : int
	{
		return (int) (((2 * $base + $iv) * $level / 100 + 5) * $natureModifier * $friendshipModifier) + $av;
	}

	/**
	 * Calculate a Pokémon's Friendship modifier for Let's Go, Pikachu! or Let's Go, Eevee!
	 */
	public function letsGoFriendshipModifier(int $friendship) : float
	{
		return 1 + (int) (10 * $friendship / 255) / 100;
	}

	/**
	 * Calculate a Pokémon's CP for Let's Go, Pikachu! or Let's Go, Eevee!
	 *
	 * @param int[] $finalStats
	 * @param int[] $avs
	 */
	public function letsGoCp(int $level, array $finalStats, array $avs) : int
	{
		$sumStats = array_sum($finalStats);
		$sumAvs = array_sum($avs);

		return min((int) (($sumStats - $sumAvs) * $level * 6 / 100 + $sumAvs * ($level * 4 / 100 + 2)), 10000);
	}

	/**
	 * Calculate a Pokémon's HP stat in Legends: Arceus.
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Pok%C3%A9mon_Legends:_Arceus
	 */
	public function legendsHp(int $base, int $level, int $effortLevel) : int
	{
		$elb = $this->getEffortLevelBonus($base, $level, $effortLevel);

		return (int) (($level / 100 + 1) * $base + $level) + $elb;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in Legends: Arceus.
	 * https://bulbapedia.bulbagarden.net/wiki/Stat#Pok%C3%A9mon_Legends:_Arceus
	 */
	public function legendsOther(int $base, int $level, int $effortLevel, float $natureModifier) : int
	{
		$elb = $this->getEffortLevelBonus($base, $level, $effortLevel);

		return (int) ((int) (($level / 50 + 1) * $base / 1.5) * $natureModifier) + $elb;
	}

	/**
	 * Get the effort level bonus.
	 */
	private function getEffortLevelBonus(int $base, int $level, int $effortLevel) : int
	{
		$multiplier = $this->getEffortLevelMultiplier($effortLevel);

		return (int) round((sqrt($base) * $multiplier + $level) / 2.5);
	}

	/**
	 * Get the multiplier for this effort level, used in the effort level bonus.
	 */
	private function getEffortLevelMultiplier(int $effortLevel) : int
	{
		return match ($effortLevel) {
			0 => 0,
			1 => 2,
			2 => 3,
			3 => 4,
			4 => 7,
			5 => 8,
			6 => 9,
			7 => 14,
			8 => 15,
			9 => 16,
			10 => 25,
			default => throw new Exception("Invalid effort level: $effortLevel."),
		};
	}

	/**
	 * Get the perfect IV value for this generation.
	 */
	public function getPerfectIv(GenerationId $generationId) : int
	{
		$generation = $generationId->value;

		if ($generation === 1 || $generation === 2) {
			return self::PERFECT_IV_GEN_1;
		}

		return self::PERFECT_IV_GEN_3;
	}
}
