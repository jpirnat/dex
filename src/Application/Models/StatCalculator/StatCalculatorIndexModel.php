<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatCalculator;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorPokemon;
use Jp\Dex\Application\Models\IvCalculator\IvCalculatorQueriesInterface;
use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class StatCalculatorIndexModel
{
	/** @var IvCalculatorPokemon[] $pokemons */ private(set) array $pokemons = [];
	private(set) array $natures = [];
	private(set) array $stats = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly IvCalculatorQueriesInterface $queries,
	) {}


	/**
	 * Set data for the stat calculator page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

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
}
