<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Abilities\AbilityName;
use Jp\Dex\Domain\Formats\FormatName;
use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Types\Type;

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
	 * @param Type $pokemonType
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		FormatName $formatName,
		int $rating,
		PokemonName $pokemonName,
		AbilityName $abilityName,
		Type $pokemonType,
		array $trendPoints
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->abilityName = $abilityName;
		$this->pokemonType = $pokemonType;

		foreach ($trendPoints as $trendPoint) {
			$this->addTrendPoint($trendPoint);
		}
	}

	/**
	 * Get the title of a chart that consists of only this trend line.
	 *
	 * @return string
	 */
	public function getChartTitle() : string
	{
		$formatName = $this->formatName->getName();
		$rating = $this->rating;
		$pokemonName = $this->pokemonName->getName();
		$abilityName = $this->abilityName->getName();

		return "$formatName [$rating] $pokemonName with $abilityName Usage";
	}

	/**
	 * Get the trend line's label, for a chart that consists of only this trend
	 * line.
	 *
	 * @return string
	 */
	public function getLineLabel() : string
	{
		return 'Usage %';
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
