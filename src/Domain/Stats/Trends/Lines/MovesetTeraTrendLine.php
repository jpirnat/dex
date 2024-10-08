<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Trends\Lines;

use Jp\Dex\Domain\Pokemon\PokemonName;
use Jp\Dex\Domain\Types\Type;

final class MovesetTeraTrendLine extends TrendLine
{
	private string $typeName;
	private string $typeColorCode;

	/**
	 * Constructor.
	 *
	 * @param TrendPoint[] $trendPoints
	 */
	public function __construct(
		string $formatName,
		int $rating,
		PokemonName $pokemonName,
		string $typeName,
		Type $pokemonType,
		string $typeColorCode,
		array $trendPoints,
	) {
		$this->formatName = $formatName;
		$this->rating = $rating;
		$this->pokemonName = $pokemonName;
		$this->typeName = $typeName;
		$this->pokemonType = $pokemonType;
		$this->typeColorCode = $typeColorCode;

		foreach ($trendPoints as $trendPoint) {
			$this->addTrendPoint($trendPoint);
		}
	}

	/**
	 * Get the title of a chart that consists of only this trend line.
	 */
	public function getChartTitle() : string
	{
		$formatName = $this->formatName;
		$rating = $this->rating;
		$pokemonName = $this->pokemonName->getName();
		$typeName = $this->typeName;

		return "$formatName [$rating] $pokemonName - Tera $typeName Usage";
	}

	/**
	 * Get the trend line's label, for a chart that consists of only this trend
	 * line.
	 */
	public function getLineLabel() : string
	{
		return 'Usage %';
	}

	/**
	 * Get the moveset Tera trend line's type name.
	 */
	public function getTypeName() : string
	{
		return $this->typeName;
	}

	/**
	 * Get the trend line's move type.
	 */
	public function getTypeColorCode() : string
	{
		return $this->typeColorCode;
	}
}
