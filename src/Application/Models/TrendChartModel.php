<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\Language;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Languages\LanguageRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Trends\Generators\LeadUsageTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetAbilityTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetItemTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\MovesetMoveTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageAbilityTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageItemTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageMoveTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendLine;

class TrendChartModel
{
	/** @var UsageTrendGenerator $usageTrendGenerator */
	private $usageTrendGenerator;

	/** @var LeadUsageTrendGenerator $leadUsageTrendGenerator */
	private $leadUsageTrendGenerator;

	/** @var MovesetAbilityTrendGenerator $movesetAbilityTrendGenerator */
	private $movesetAbilityTrendGenerator;

	/** @var MovesetItemTrendGenerator $movesetItemTrendGenerator */
	private $movesetItemTrendGenerator;

	/** @var MovesetMoveTrendGenerator $movesetMoveTrendGenerator */
	private $movesetMoveTrendGenerator;

	/** @var UsageAbilityTrendGenerator $usageAbilityTrendGenerator */
	private $usageAbilityTrendGenerator;

	/** @var UsageItemTrendGenerator $usageItemTrendGenerator */
	private $usageItemTrendGenerator;

	/** @var UsageMoveTrendGenerator $usageMoveTrendGenerator */
	private $usageMoveTrendGenerator;

	/** @var LanguageRepositoryInterface $languageRepository */
	private $languageRepository;


	/** @var TrendLine[] $trendLines */
	private $trendLines = [];

	/** @var string[] $similarities */
	private $similarities = [];

	/** @var string[] $differences */
	private $differences = [];

	/** @var Language $language */
	private $language;


	/**
	 * Constructor.
	 *
	 * @param UsageTrendGenerator $usageTrendGenerator
	 * @param LeadUsageTrendGenerator $leadUsageTrendGenerator
	 * @param MovesetAbilityTrendGenerator $movesetAbilityTrendGenerator
	 * @param MovesetItemTrendGenerator $movesetItemTrendGenerator
	 * @param MovesetMoveTrendGenerator $movesetMoveTrendGenerator
	 * @param UsageAbilityTrendGenerator $usageAbilityTrendGenerator
	 * @param UsageItemTrendGenerator $usageItemTrendGenerator
	 * @param UsageMoveTrendGenerator $usageMoveTrendGenerator
	 * @param LanguageRepositoryInterface $languageRepository
	 */
	public function __construct(
		UsageTrendGenerator $usageTrendGenerator,
		LeadUsageTrendGenerator $leadUsageTrendGenerator,
		MovesetAbilityTrendGenerator $movesetAbilityTrendGenerator,
		MovesetItemTrendGenerator $movesetItemTrendGenerator,
		MovesetMoveTrendGenerator $movesetMoveTrendGenerator,
		UsageAbilityTrendGenerator $usageAbilityTrendGenerator,
		UsageItemTrendGenerator $usageItemTrendGenerator,
		UsageMoveTrendGenerator $usageMoveTrendGenerator,
		LanguageRepositoryInterface $languageRepository
	) {
		$this->usageTrendGenerator = $usageTrendGenerator;
		$this->leadUsageTrendGenerator = $leadUsageTrendGenerator;
		$this->movesetAbilityTrendGenerator = $movesetAbilityTrendGenerator;
		$this->movesetItemTrendGenerator = $movesetItemTrendGenerator;
		$this->movesetMoveTrendGenerator = $movesetMoveTrendGenerator;
		$this->usageAbilityTrendGenerator = $usageAbilityTrendGenerator;
		$this->usageItemTrendGenerator = $usageItemTrendGenerator;
		$this->usageMoveTrendGenerator = $usageMoveTrendGenerator;
		$this->languageRepository = $languageRepository;
	}

	/**
	 * Set the data for the requested lines to chart.
	 *
	 * @param array $lines
	 * @param LanguageId $languageId
	 *
	 * @return void
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
			$type = $line['type'];
			$formatId = new FormatId((int) $line['formatId']);
			$rating = (int) $line['rating'];
			$pokemonId = new PokemonId((int) $line['pokemonId']);

			if ($type === 'usage') {
				$this->trendLines[] = $this->usageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$languageId
				);
			}

			if ($type === 'lead-usage') {
				$this->trendLines[] = $this->leadUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$languageId
				);
			}

			if ($type === 'moveset-ability') {
				$abilityId = new AbilityId((int) $line['abilityId']);

				$this->trendLines[] = $this->movesetAbilityTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$abilityId,
					$languageId
				);
			}

			if ($type === 'moveset-item') {
				$itemId = new ItemId((int) $line['itemId']);

				$this->trendLines[] = $this->movesetItemTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$itemId,
					$languageId
				);
			}

			if ($type === 'moveset-move') {
				$moveId = new MoveId((int) $line['moveId']);

				$this->trendLines[] = $this->movesetMoveTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$moveId,
					$languageId
				);
			}

			if ($type === 'usage-ability') {
				$abilityId = new AbilityId((int) $line['abilityId']);

				$this->trendLines[] = $this->usageAbilityTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$abilityId,
					$languageId
				);
			}

			if ($type === 'usage-item') {
				$itemId = new ItemId((int) $line['itemId']);

				$this->trendLines[] = $this->usageItemTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$itemId,
					$languageId
				);
			}

			if ($type === 'usage-move') {
				$moveId = new MoveId((int) $line['moveId']);

				$this->trendLines[] = $this->usageMoveTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$moveId,
					$languageId
				);
			}
		}

		$this->findDifferences($lines);

		$this->language = $this->languageRepository->getById($languageId);
	}

	/**
	 * Is this line valid?
	 *
	 * @param array $line
	 *
	 * @return bool
	 */
	private function isValid(array $line) : bool
	{
		// Required parameters for every chart type.
		if (!isset($line['type'])
			|| !isset($line['formatId'])
			|| !isset($line['rating'])
			|| !isset($line['pokemonId'])
		) {
			return false;
		}

		$type = $line['type'];

		// The current list of accepted chart types.
		if ($type !== 'usage'
			&& $type !== 'lead-usage'
			&& $type !== 'moveset-ability'
			&& $type !== 'moveset-item'
			&& $type !== 'moveset-move'
			&& $type !== 'usage-ability'
			&& $type !== 'usage-item'
			&& $type !== 'usage-move'
		) {
			return false;
		}

		// Optional parameters for certain chart types.
		if (($type === 'moveset-ability' || $type === 'usage-ability') && !isset($line['abilityId'])) {
			return false;
		}

		if (($type === 'moveset-item' || $type === 'usage-item') && !isset($line['itemId'])) {
			return false;
		}

		if (($type === 'moveset-move' || $type === 'usage-move') && !isset($line['moveId'])) {
			return false;
		}

		return true;
	}

	/**
	 * Determine which variables are different across the requested lines, so
	 * we can dynamically generate the chart title and line labels.
	 *
	 * @param array $lines
	 *
	 * @return void
	 */
	private function findDifferences(array $lines) : void
	{
		$types = [];
		$formatIds = [];
		$ratings = [];
		$pokemonIds = [];
		$abilityIds = [];
		$itemIds = [];
		$moveIds = [];
		$this->similarities = [];
		$this->differences = [];

		foreach ($lines as $line) {
			$types[$line['type']] = $line['type'];
			$formatIds[$line['formatId']] = $line['formatId'];
			$ratings[$line['rating']] = $line['rating'];
			$pokemonIds[$line['pokemonId']] = $line['pokemonId'];
			if (isset($line['abilityId'])) {
				$abilityIds[$line['abilityId']] = $line['abilityId'];
			}
			if (isset($line['itemId'])) {
				$itemIds[$line['itemId']] = $line['itemId'];
			}
			if (isset($line['moveId'])) {
				$moveIds[$line['moveId']] = $line['moveId'];
			}
		}

		if (count($types) === 1) {
			$this->similarities[] = 'type';
		}
		if (count($formatIds) === 1) {
			$this->similarities[] = 'format';
		}
		if (count($ratings) === 1) {
			$this->similarities[] = 'rating';
		}
		if (count($pokemonIds) === 1) {
			$this->similarities[] = 'pokemon';
		}
		if (count($abilityIds) + count($itemIds) + count($moveIds) === 1) {
			$this->similarities[] = 'moveset';
		}

		if (count($types) > 1) {
			$this->differences[] = 'type';
		}
		if (count($formatIds) > 1) {
			$this->differences[] = 'format';
		}
		if (count($ratings) > 1) {
			$this->differences[] = 'rating';
		}
		if (count($pokemonIds) > 1) {
			$this->differences[] = 'pokemon';
		}
		if (count($abilityIds) + count($itemIds) + count($moveIds) > 1) {
			$this->differences[] = 'moveset';
		}
	}

	/**
	 * Get the trend lines.
	 *
	 * @return TrendLine[]
	 */
	public function getTrendLines() : array
	{
		return $this->trendLines;
	}

	/**
	 * Get the similarities between lines.
	 *
	 * @return string[]
	 */
	public function getSimilarities() : array
	{
		return $this->similarities;
	}

	/**
	 * Get the differences between lines.
	 *
	 * @return string[]
	 */
	public function getDifferences() : array
	{
		return $this->differences;
	}

	/**
	 * Get the language.
	 *
	 * @return Language
	 */
	public function getLanguage() : Language
	{
		return $this->language;
	}
}
