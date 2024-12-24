<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\IvCalculator;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Languages\LanguageId;

final class IvCalculatorIndexModel
{
	/** @var IvCalculatorPokemon[] $pokemons */ private(set) array $pokemons = [];
	private(set) array $natures = [];
	private(set) array $characteristics = [];
	private(set) array $types = [];
	private(set) array $stats = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
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

		$this->versionGroupModel->setWithStatFormulaType('gen3');

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

		$versionGroup = $this->versionGroupModel->versionGroup;
		if ($versionGroup->hasIvBasedHiddenPower) {
			$this->types = $this->queries->getTypes(
				$versionGroupId,
				$languageId,
			);
		}

		$this->stats = $this->queries->getStats($versionGroupId, $languageId);
	}
}
