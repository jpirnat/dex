<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

use Jp\Dex\Domain\Types\Type;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class HiddenPowerCalculator
{
	private TypeRepositoryInterface $typeRepository;

	/**
	 * Constructor.
	 *
	 * @param TypeRepositoryInterface $typeRepository
	 */
	public function __construct(TypeRepositoryInterface $typeRepository)
	{
		$this->typeRepository = $typeRepository;
	}

	/**
	 * Calculate a Pokémon's Hidden Power type in Generation II
	 *
	 * @param int $atk
	 * @param int $def
	 *
	 * @return Type
	 */
	public function type2(int $atk, int $def) : Type
	{
		$hiddenPowerIndex = 4 * $atk % 4 + $def % 4;

		return $this->typeRepository->getByHiddenPowerIndex($hiddenPowerIndex);
	}

	/**
	 * Calculate a Pokémon's Hidden Power type in Generations III through VII.
	 *
	 * @param int $hp
	 * @param int $atk
	 * @param int $def
	 * @param int $spe
	 * @param int $spa
	 * @param int $spd
	 *
	 * @return Type
	 */
	public function type3(
		int $hp,
		int $atk,
		int $def,
		int $spe,
		int $spa,
		int $spd
	) : Type {
		$a = $hp % 2;
		$b = $atk % 2;
		$c = $def % 2;
		$d = $spe % 2;
		$e = $spa % 2;
		$f = $spd % 2;

		$hiddenPowerIndex = (int) floor(($a + 2 * $b + 4 * $c + 8 * $d + 16 * $e + 32 * $f) * 15 / 63);

		return $this->typeRepository->getByHiddenPowerIndex($hiddenPowerIndex);
	}

	/**
	 * Calculate a Pokémon's Hidden Power base power in Generation II.
	 *
	 * @param int $atk
	 * @param int $def
	 * @param int $spe
	 * @param int $spc
	 *
	 * @return int
	 */
	public function power2(
		int $atk,
		int $def,
		int $spe,
		int $spc
	) : int {
		$v = $spc < 8 ? 0 : 1;
		$w = $spe < 8 ? 0 : 1;
		$x = $def < 8 ? 0 : 1;
		$y = $atk < 8 ? 0 : 1;
		$z = $spc % 4;

		return (int) ((5 * ($v + 2 * $w + 4 * $x + 8 * $y) + $z) / 2 + 31);
	}

	/**
	 * Calculate a Pokémon's Hidden Power base power in Generations III through V.
	 *
	 * @param int $hp
	 * @param int $atk
	 * @param int $def
	 * @param int $spe
	 * @param int $spa
	 * @param int $spd
	 *
	 * @return int
	 */
	public function power3(
		int $hp,
		int $atk,
		int $def,
		int $spe,
		int $spa,
		int $spd
	) : int {
		$u = ($hp % 4 === 2 || $hp % 4 === 3) ? 1 : 0;
		$v = ($atk % 4 === 2 || $atk % 4 === 3) ? 1 : 0;
		$w = ($def % 4 === 2 || $def % 4 === 3) ? 1 : 0;
		$x = ($spe % 4 === 2 || $spe % 4 === 3) ? 1 : 0;
		$y = ($spa % 4 === 2 || $spa % 4 === 3) ? 1 : 0;
		$z = ($spd % 4 === 2 || $spd % 4 === 3) ? 1 : 0;

		return (int) ((($u + 2 * $v + 4 * $w + 8 * $x + 16 * $y + 32 * $z) * 40) / 63 + 30);
	}

	/**
	 * Calculate a Pokémon's Hidden Power base power in Generations VI through VII.
	 *
	 * @return int
	 */
	public function power6() : int
	{
		return 60;
	}
}
