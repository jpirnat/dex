<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Stats\Trends\LeadUsageTrendGenerator;
use Jp\Dex\Stats\Trends\MovesetAbilityTrendGenerator;
use Jp\Dex\Stats\Trends\MovesetItemTrendGenerator;
use Jp\Dex\Stats\Trends\MovesetMoveTrendGenerator;
use Jp\Dex\Stats\Trends\UsageAbilityTrendGenerator;
use Jp\Dex\Stats\Trends\UsageItemTrendGenerator;
use Jp\Dex\Stats\Trends\UsageMoveTrendGenerator;
use Jp\Dex\Stats\Trends\UsageTrendGenerator;

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
	 */
	public function __construct(
		UsageTrendGenerator $usageTrendGenerator,
		LeadUsageTrendGenerator $leadUsageTrendGenerator,
		MovesetAbilityTrendGenerator $movesetAbilityTrendGenerator,
		MovesetItemTrendGenerator $movesetItemTrendGenerator,
		MovesetMoveTrendGenerator $movesetMoveTrendGenerator,
		UsageAbilityTrendGenerator $usageAbilityTrendGenerator,
		UsageItemTrendGenerator $usageItemTrendGenerator,
		UsageMoveTrendGenerator $usageMoveTrendGenerator
	) {
		$this->usageTrendGenerator = $usageTrendGenerator;
		$this->leadUsageTrendGenerator = $leadUsageTrendGenerator;
		$this->movesetAbilityTrendGenerator = $movesetAbilityTrendGenerator;
		$this->movesetItemTrendGenerator = $movesetItemTrendGenerator;
		$this->movesetMoveTrendGenerator = $movesetMoveTrendGenerator;
		$this->usageAbilityTrendGenerator = $usageAbilityTrendGenerator;
		$this->usageItemTrendGenerator = $usageItemTrendGenerator;
		$this->usageMoveTrendGenerator = $usageMoveTrendGenerator;
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
		foreach ($lines as $line) {
			// Required parameters for every chart type.
			if (!isset($line['type'])
				|| !isset($line['formatId'])
				|| !isset($line['rating'])
				|| !isset($line['pokemonId'])
			) {
				continue;
			}

			$type = $line['type'];
			$formatId = new FormatId((int) $line['formatId']);
			$rating = (int) $line['rating'];
			$pokemonId = new PokemonId((int) $line['pokemonId']);

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
				continue;
			}

			if ($type === 'usage') {
				$trendLine = $this->usageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$languageId
				);
			}

			if ($type === 'lead-usage') {
				$trendLine = $this->leadUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$languageId
				);
			}

			if ($type === 'moveset-ability') {
				if (!isset($line['abilityId'])) {
					continue;
				}

				$abilityId = new AbilityId((int) $line['abilityId']);

				$trendLine = $this->movesetAbilityTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$abilityId,
					$languageId
				);
			}

			if ($type === 'moveset-item') {
				if (!isset($line['itemId'])) {
					continue;
				}

				$itemId = new ItemId((int) $line['itemId']);

				$trendLine = $this->movesetItemTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$itemId,
					$languageId
				);
			}

			if ($type === 'moveset-move') {
				if (!isset($line['moveId'])) {
					continue;
				}

				$moveId = new MoveId((int) $line['moveId']);

				$trendLine = $this->movesetMoveTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$moveId,
					$languageId
				);
			}

			if ($type === 'usage-ability') {
				if (!isset($line['abilityId'])) {
					continue;
				}

				$abilityId = new AbilityId((int) $line['abilityId']);

				$trendLine = $this->usageAbilityTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$abilityId,
					$languageId
				);
			}

			if ($type === 'usage-item') {
				if (!isset($line['itemId'])) {
					continue;
				}

				$itemId = new ItemId((int) $line['itemId']);

				$trendLine = $this->usageItemTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$itemId,
					$languageId
				);
			}

			if ($type === 'usage-move') {
				if (!isset($line['moveId'])) {
					continue;
				}

				$moveId = new MoveId((int) $line['moveId']);

				$trendLine = $this->usageMoveTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$moveId,
					$languageId
				);
			}
		}

		// Next, put it all together.
	}
}
