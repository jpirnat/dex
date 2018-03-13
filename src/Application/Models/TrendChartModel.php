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
use Jp\Dex\Domain\Stats\Trends\Generators\UsageAbilityTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageItemTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageMoveTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Generators\UsageTrendGenerator;
use Jp\Dex\Domain\Stats\Trends\Lines\TrendLine;

class TrendChartModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	private $itemRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

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
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param ItemRepositoryInterface $itemRepository
	 * @param MoveRepositoryInterface $moveRepository
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
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		AbilityRepositoryInterface $abilityRepository,
		ItemRepositoryInterface $itemRepository,
		MoveRepositoryInterface $moveRepository,
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
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->abilityRepository = $abilityRepository;
		$this->itemRepository = $itemRepository;
		$this->moveRepository = $moveRepository;
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
			$format = $this->formatRepository->getByIdentifier($line['format']);
			$rating = (int) $line['rating'];
			$pokemon = $this->pokemonRepository->getByIdentifier($line['pokemon']);

			if ($type === 'usage') {
				$this->trendLines[] = $this->usageTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$languageId
				);
			}

			if ($type === 'lead-usage') {
				$this->trendLines[] = $this->leadUsageTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$languageId
				);
			}

			if ($type === 'moveset-ability') {
				$ability = $this->abilityRepository->getByIdentifier($line['ability']);

				$this->trendLines[] = $this->movesetAbilityTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$ability->getId(),
					$languageId
				);
			}

			if ($type === 'moveset-item') {
				$item = $this->itemRepository->getByIdentifier($line['item']);

				$this->trendLines[] = $this->movesetItemTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$item->getId(),
					$languageId
				);
			}

			if ($type === 'moveset-move') {
				$move = $this->moveRepository->getByIdentifier($line['move']);

				$this->trendLines[] = $this->movesetMoveTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$move->getId(),
					$languageId
				);
			}

			if ($type === 'usage-ability') {
				$ability = $this->abilityRepository->getByIdentifier($line['ability']);

				$this->trendLines[] = $this->usageAbilityTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$ability->getId(),
					$languageId
				);
			}

			if ($type === 'usage-item') {
				$item = $this->itemRepository->getByIdentifier($line['item']);

				$this->trendLines[] = $this->usageItemTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$item->getId(),
					$languageId
				);
			}

			if ($type === 'usage-move') {
				$move = $this->moveRepository->getByIdentifier($line['move']);

				$this->trendLines[] = $this->usageMoveTrendGenerator->generate(
					$format,
					$rating,
					$pokemon->getId(),
					$move->getId(),
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
			|| !isset($line['format'])
			|| !isset($line['rating'])
			|| !isset($line['pokemon'])
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
		if (($type === 'moveset-ability' || $type === 'usage-ability') && !isset($line['ability'])) {
			return false;
		}

		if (($type === 'moveset-item' || $type === 'usage-item') && !isset($line['item'])) {
			return false;
		}

		if (($type === 'moveset-move' || $type === 'usage-move') && !isset($line['move'])) {
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
		$formats = [];
		$ratings = [];
		$pokemon = [];
		$abilities = [];
		$items = [];
		$moves = [];
		$this->similarities = [];
		$this->differences = [];

		foreach ($lines as $line) {
			$types[$line['type']] = $line['type'];
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
		}

		if (count($types) === 1) {
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

		if (count($types) > 1) {
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
		if (count($abilities) + count($items) + count($moves) > 1) {
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
