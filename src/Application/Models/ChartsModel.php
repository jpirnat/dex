<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Stats\Trends\AbilityUsageTrendGenerator;
use Jp\Dex\Stats\Trends\ItemUsageTrendGenerator;
use Jp\Dex\Stats\Trends\LeadUsageTrendGenerator;
use Jp\Dex\Stats\Trends\MoveUsageTrendGenerator;
use Jp\Dex\Stats\Trends\UsageTrendGenerator;

class ChartsModel
{
	/** @var UsageTrendGenerator $usageTrendGenerator */
	private $usageTrendGenerator;

	/** @var LeadUsageTrendGenerator $leadUsageTrendGenerator */
	private $leadUsageTrendGenerator;

	/** @var AbilityUsageTrendGenerator $abilityUsageTrendGenerator */
	private $abilityUsageTrendGenerator;

	/** @var ItemUsageTrendGenerator $itemUsageTrendGenerator */
	private $itemUsageTrendGenerator;

	/** @var MoveUsageTrendGenerator $moveUsageTrendGenerator */
	private $moveUsageTrendGenerator;

	/**
	 * Constructor.
	 *
	 * @param UsageTrendGenerator $usageTrendGenerator
	 * @param LeadUsageTrendGenerator $leadUsageTrendGenerator
	 * @param AbilityUsageTrendGenerator $abilityUsageTrendGenerator
	 * @param ItemUsageTrendGenerator $itemUsageTrendGenerator
	 * @param MoveUsageTrendGenerator $moveUsageTrendGenerator
	 */
	public function __construct(
		UsageTrendGenerator $usageTrendGenerator,
		LeadUsageTrendGenerator $leadUsageTrendGenerator,
		AbilityUsageTrendGenerator $abilityUsageTrendGenerator,
		ItemUsageTrendGenerator $itemUsageTrendGenerator,
		MoveUsageTrendGenerator $moveUsageTrendGenerator
	) {
		$this->usageTrendGenerator = $usageTrendGenerator;
		$this->leadUsageTrendGenerator = $leadUsageTrendGenerator;
		$this->abilityUsageTrendGenerator = $abilityUsageTrendGenerator;
		$this->itemUsageTrendGenerator = $itemUsageTrendGenerator;
		$this->moveUsageTrendGenerator = $moveUsageTrendGenerator;
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
			$rating = $line['rating'];
			$pokemonId = new PokemonId((int) $line['pokemonId']);

			// The current list of accepted chart types.
			if ($type !== 'usage'
				&& $type !== 'lead-usage'
				&& $type !== 'ability-usage'
				&& $type !== 'item-usage'
				&& $type !== 'move-usage'
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

			if ($type === 'ability-usage') {
				if (!isset($line['abilityId'])) {
					continue;
				}

				$abilityId = new AbilityId((int) $line['abilityId']);

				$trendLine = $this->abilityUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$abilityId,
					$languageId
				);
			}

			if ($type === 'item-usage') {
				if (!isset($line['itemId'])) {
					continue;
				}

				$itemId = new ItemId((int) $line['itemId']);

				$trendLine = $this->itemUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$itemId,
					$languageId
				);
			}

			if ($type === 'move-usage') {
				if (!isset($line['moveId'])) {
					continue;
				}

				$moveId = new MoveId((int) $line['moveId']);

				$trendLine = $this->moveUsageTrendGenerator->generate(
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
