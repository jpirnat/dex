<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Pokemon\PokemonName;

class UsageAbilityTrendLine extends TrendLine
{
	/** @var AbilityName $abilityName */
	private $abilityName;

	/**
	 * Constructor.
	 *
	 * @param FormatName $formatName
	 * @param int $rating
	 * @param PokemonName $pokemonName
	 * @param AbilityName $abilityName
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		FormatName $formatName,
		int $rating,
		PokemonName $pokemonName,
		AbilityName $abilityName,
		array $trendPoints
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->abilityName = $abilityName;

		foreach ($trendPoints as $trendPoint) {
			$this->addTrendPoint($trendPoint);
		}
	}

	/**
	 * Get the usage ability trend line's ability name.
	 *
	 * @return AbilityName
	 */
	public function getAbilityName() : AbilityName
	{
		return $this->abilityName;
	}
}
