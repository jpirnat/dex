<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Languages\Language;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Trends\Generators\LeadUsageTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetAbilityTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetItemTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetMoveTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetTeraTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageAbilityTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageItemTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageMoveTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendLine;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class StatsChartModel
{
	/** @var TrendLine[] $trendLines */
	private(set) array $trendLines = [];

	/** @var string[] $similarities */
	private(set) array $similarities = [];

	/** @var string[] $differences */
	private(set) array $differences = [];

	private(set) Language $language;


	public function __construct(
		private readonly FormatRepositoryInterface $formatRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly AbilityRepositoryInterface $abilityRepository,
		private readonly ItemRepositoryInterface $itemRepository,
		private readonly MoveRepositoryInterface $moveRepository,
		private readonly TypeRepositoryInterface $typeRepository,
		private readonly UsageTrendGenerator $usageTrendGenerator,
		private readonly LeadUsageTrendGenerator $leadUsageTrendGenerator,
		private readonly MovesetAbilityTrendGenerator $movesetAbilityTrendGenerator,
		private readonly MovesetItemTrendGenerator $movesetItemTrendGenerator,
		private readonly MovesetMoveTrendGenerator $movesetMoveTrendGenerator,
		private readonly MovesetTeraTrendGenerator $movesetTeraTrendGenerator,
		private readonly UsageAbilityTrendGenerator $usageAbilityTrendGenerator,
		private readonly UsageItemTrendGenerator $usageItemTrendGenerator,
		private readonly UsageMoveTrendGenerator $usageMoveTrendGenerator,
		private readonly LanguageRepositoryInterface $languageRepository,
	) {}


	/**
	 * Set the data for the requested lines to chart.
	 */
	public function setData(array $lines, LanguageId $languageId) : void
	{
		$this->trendLines = [];

		// Remove lines with invalid data from the... lineup.
		$validLines = [];
		foreach ($lines as $line) {
			if ($this->isValid($line)) {
				$validLines[] = $line;
			}
		}

		// Create a trend line object from each valid line request.
		foreach ($validLines as $line) {
			$lineType = $line['type'];
			$format = $this->formatRepository->getByIdentifier(
				$line['format'],
				$languageId,
			);
			$rating = (int) $line['rating'];
			$pokemon = $this->pokemonRepository->getByIdentifier($line['pokemon']);

			if ($lineType === 'usage') {
				$this->trendLines[] = $this->usageTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$languageId,
				);
			}

			if ($lineType === 'lead-usage') {
				$this->trendLines[] = $this->leadUsageTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$languageId,
				);
			}

			if ($lineType === 'moveset-ability') {
				$ability = $this->abilityRepository->getByIdentifier($line['ability']);

				$this->trendLines[] = $this->movesetAbilityTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$ability->getId(),
					$languageId,
				);
			}

			if ($lineType === 'moveset-item') {
				$item = $this->itemRepository->getByIdentifier($line['item']);

				$this->trendLines[] = $this->movesetItemTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$item->getId(),
					$languageId,
				);
			}

			if ($lineType === 'moveset-move') {
				$move = $this->moveRepository->getByIdentifier($line['move']);

				$this->trendLines[] = $this->movesetMoveTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$move->getId(),
					$languageId,
				);
			}

			if ($lineType === 'moveset-tera') {
				$type = $this->typeRepository->getByIdentifier($line['tera']);

				$this->trendLines[] = $this->movesetTeraTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$type->getId(),
					$languageId,
				);
			}

			if ($lineType === 'usage-ability') {
				$ability = $this->abilityRepository->getByIdentifier($line['ability']);

				$this->trendLines[] = $this->usageAbilityTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$ability->getId(),
					$languageId,
				);
			}

			if ($lineType === 'usage-item') {
				$item = $this->itemRepository->getByIdentifier($line['item']);

				$this->trendLines[] = $this->usageItemTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$item->getId(),
					$languageId,
				);
			}

			if ($lineType === 'usage-move') {
				$move = $this->moveRepository->getByIdentifier($line['move']);

				$this->trendLines[] = $this->usageMoveTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$move->getId(),
					$languageId,
				);
			}
		}

		$this->findDifferences($lines);

		$this->language = $this->languageRepository->getById($languageId);
	}

	/**
	 * Is this line valid?
	 */
	private function isValid(array $line) : bool
	{
		// Required parameters for every chart type.
		if (!isset($line['type'])
			|| !isset($line['format'])
			|| !isset($line['rating'])
			|| !isset($line['pokemon'])
		) {
			return false;
		}

		$lineType = $line['type'];

		// The current list of accepted chart types.
		if ($lineType !== 'usage'
			&& $lineType !== 'lead-usage'
			&& $lineType !== 'moveset-ability'
			&& $lineType !== 'moveset-item'
			&& $lineType !== 'moveset-move'
			&& $lineType !== 'moveset-tera'
			&& $lineType !== 'usage-ability'
			&& $lineType !== 'usage-item'
			&& $lineType !== 'usage-move'
		) {
			return false;
		}

		// Optional parameters for certain chart types.
		if (($lineType === 'moveset-ability' || $lineType === 'usage-ability') && !isset($line['ability'])) {
			return false;
		}

		if (($lineType === 'moveset-item' || $lineType === 'usage-item') && !isset($line['item'])) {
			return false;
		}

		if (($lineType === 'moveset-move' || $lineType === 'usage-move') && !isset($line['move'])) {
			return false;
		}

		if ($lineType === 'moveset-tera' && !isset($line['tera'])) {
			return false;
		}

		return true;
	}

	/**
	 * Determine which variables are different across the requested lines, so
	 * we can dynamically generate the chart title and line labels.
	 */
	private function findDifferences(array $lines) : void
	{
		$lineTypes = [];
		$formats = [];
		$ratings = [];
		$pokemon = [];
		$abilities = [];
		$items = [];
		$moves = [];
		$teraTypes = [];
		$this->similarities = [];
		$this->differences = [];

		foreach ($lines as $line) {
			$lineTypes[$line['type']] = $line['type'];
			$formats[$line['format']] = $line['format'];
			$ratings[$line['rating']] = $line['rating'];
			$pokemon[$line['pokemon']] = $line['pokemon'];
			if (isset($line['ability'])) {
				$abilities[$line['ability']] = $line['ability'];
			}
			if (isset($line['item'])) {
				$items[$line['item']] = $line['item'];
			}
			if (isset($line['move'])) {
				$moves[$line['move']] = $line['move'];
			}
			if (isset($line['tera'])) {
				$teraTypes[$line['tera']] = $line['tera'];
			}
		}

		if (count($lineTypes) === 1) {
			$this->similarities[] = 'type';
		}
		if (count($formats) === 1) {
			$this->similarities[] = 'format';
		}
		if (count($ratings) === 1) {
			$this->similarities[] = 'rating';
		}
		if (count($pokemon) === 1) {
			$this->similarities[] = 'pokemon';
		}
		if (count($abilities) + count($items) + count($moves) === 1) {
			$this->similarities[] = 'moveset';
		}

		if (count($lineTypes) > 1) {
			$this->differences[] = 'type';
		}
		if (count($formats) > 1) {
			$this->differences[] = 'format';
		}
		if (count($ratings) > 1) {
			$this->differences[] = 'rating';
		}
		if (count($pokemon) > 1) {
			$this->differences[] = 'pokemon';
		}
		if (count($abilities) + count($items) + count($moves) + count($teraTypes) > 1) {
			$this->differences[] = 'moveset';
		}
	}
}
