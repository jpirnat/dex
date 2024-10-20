<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\EvCalculator;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorPokemon;
use Jp\Dex\Application\Models\IvCalculator\IvCalculatorQueriesInterface;
use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Languages\LanguageId;

final class EvCalculatorIndexModel
{
	/** @var IvCalculatorPokemon[] $pokemons */ private array $pokemons = [];
	private array $natures = [];
	private array $stats = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly IvCalculatorQueriesInterface $queries,
	) {}


	/**
	 * Set data for the EV calculator page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithEvBasedStats();

		$this->pokemons = $this->queries->getPokemons($versionGroupId, $languageId);

		$natures = $this->queries->getNatures($languageId);
		foreach ($natures as $nature) {
			$name = $nature['name'];
			$increasedStatAbbreviation = $nature['increasedStatAbbreviation'];
			$decreasedStatAbbreviation = $nature['decreasedStatAbbreviation'];
			$expandedName = $increasedStatAbbreviation
				? "$name (+$increasedStatAbbreviation/-$decreasedStatAbbreviation)"
				: "$name (Neutral)";

			$this->natures[] = [
				'identifier' => $nature['identifier'],
				'expandedName' => $expandedName,
			];
		}

		$this->stats = $this->queries->getStats($versionGroupId, $languageId);
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * @return IvCalculatorPokemon[]
	 */
	public function getPokemons() : array
	{
		return $this->pokemons;
	}

	public function getNatures() : array
	{
		return $this->natures;
	}

	public function getStats() : array
	{
		return $this->stats;
	}
}
