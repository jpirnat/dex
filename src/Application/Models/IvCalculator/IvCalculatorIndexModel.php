<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\IvCalculator;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Languages\LanguageId;

final class IvCalculatorIndexModel
{
	private array $pokemons = [];
	private array $natures = [];
	private array $characteristics = [];
	private array $types = [];
	private array $stats = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly IvCalculatorQueriesInterface $queries,
	) {}


	/**
	 * Set data for the IV calculator page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->pokemons = [];
		$this->natures = [];
		$this->characteristics = [];
		$this->types = [];
		$this->stats = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithIvs();

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

		$this->characteristics = $this->queries->getCharacteristics(
			$versionGroupId,
			$languageId,
		);

		$versionGroup = $this->versionGroupModel->getVersionGroup();
		if ($versionGroup->hasTypedHiddenPower()) {
			$this->types = $this->queries->getTypes(
				$versionGroupId,
				$languageId,
			);
		}

		$this->stats = $this->queries->getStats($versionGroupId, $languageId);
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getPokemons() : array
	{
		return $this->pokemons;
	}

	public function getNatures() : array
	{
		return $this->natures;
	}

	public function getCharacteristics() : array
	{
		return $this->characteristics;
	}

	public function getTypes() : array
	{
		return $this->types;
	}

	public function getStats() : array
	{
		return $this->stats;
	}
}
