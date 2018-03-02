<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

class ChartsModel
{
	/** @var UsageTrendGenerator $usageTrendGenerator */
	private $usageTrendGenerator;

	/** @var LeadsUsageTrendGenerator $leadsUsageTrendGenerator */
	private $leadsUsageTrendGenerator;

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
	 * @param LeadsUsageTrendGenerator $leadsUsageTrendGenerator
	 * @param AbilityUsageTrendGenerator $abilityUsageTrendGenerator
	 * @param ItemUsageTrendGenerator $itemUsageTrendGenerator
	 * @param MoveUsageTrendGenerator $moveUsageTrendGenerator
	 */
	public function __construct(
		UsageTrendGenerator $usageTrendGenerator,
		LeadsUsageTrendGenerator $leadsUsageTrendGenerator,
		AbilityUsageTrendGenerator $abilityUsageTrendGenerator,
		ItemUsageTrendGenerator $itemUsageTrendGenerator,
		MoveUsageTrendGenerator $moveUsageTrendGenerator
	) {
		$this->usageTrendGenerator = $usageTrendGenerator;
		$this->leadsUsageTrendGenerator = $leadsUsageTrendGenerator;
		$this->abilityUsageTrendGenerator = $abilityUsageTrendGenerator;
		$this->itemUsageTrendGenerator = $itemUsageTrendGenerator;
		$this->moveUsageTrendGenerator = $moveUsageTrendGenerator;
	}

	/**
	 * Set the data for the requested lines to chart.
	 *
	 * @param array $lines
	 *
	 * @return void
	 */
	public function setData(array $lines) : void
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

			$formatId = new FormatId($line['formatId']);
			$rating = $line['rating'];
			$pokemonId = new PokemonId($line['pokemonId']);

			// The current list of accepted chart types.
			if ($type !== 'usage'
				&& $type !== 'leads-usage'
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
					$pokemonId
				);
			}

			if ($type === 'leads-usage') {
				$trendLine = $this->leadsUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId
				);
			}

			if ($type === 'ability-usage') {
				if (!isset($line['abilityId'])) {
					continue;
				}

				$abilityId = new AbilityId($line['abilityId']);

				$trendLine = $this->abilityUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$abilityId
				);
			}

			if ($type === 'item-usage') {
				if (!isset($line['itemId'])) {
					continue;
				}

				$itemId = new ItemId($line['itemId']);

				$trendLine = $this->itemUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$itemId
				);
			}

			if ($type === 'move-usage') {
				if (!isset($line['moveId'])) {
					continue;
				}

				$moveId = new MoveId($line['moveId']);

				$trendLine = $this->moveUsageTrendGenerator->generate(
					$formatId,
					$rating,
					$pokemonId,
					$moveId
				);
			}
		}

		// Next, put it all together.
	}
}
