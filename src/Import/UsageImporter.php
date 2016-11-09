<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Import;

use Exception;
use Jp\Trendalyzer\Import\Extractors\UsageExtractor;
use Jp\Trendalyzer\Repositories\PokemonRepository;
use Jp\Trendalyzer\Repositories\Usage\UsagePokemonRepository;
use Jp\Trendalyzer\Repositories\Usage\UsageRatedPokemonRepository;
use Jp\Trendalyzer\Repositories\Usage\UsageRatedRepository;
use Jp\Trendalyzer\Repositories\Usage\UsageRepository;

class UsageImporter
{
	/** @var PokemonRepository $pokemonRepository */
	protected $pokemonRepository;

	/** @var UsageRepository $usageRepository */
	protected $usageRepository;

	/** @var UsageRatedRepository $usageRatedRepository */
	protected $usageRatedRepository;

	/** @var UsagePokemonRepository $usagePokemonRepository */
	protected $usagePokemonRepository;

	/** @var UsageRatedPokemonRepository $usageRatedPokemonRepository */
	protected $usageRatedPokemonRepository;

	/** @var UsageExtractor $usageExtractor */
	protected $usageExtractor;

	/**
	 * Constructor.
	 *
	 * @param PokemonRepository $pokemonRepository
	 * @param UsageRepository $usageRepository
	 * @param UsageRatedRepository $usageRatedRepository
	 * @param UsagePokemonRepository $usagePokemonRepository
	 * @param UsageRatedPokemonRepository $usageRatedPokemonRepository
	 * @param UsageExtractor $usageExtractor
	 */
	public function __construct(
		PokemonRepository $pokemonRepository,
		UsageRepository $usageRepository,
		UsageRatedRepository $usageRatedRepository,
		UsagePokemonRepository $usagePokemonRepository,
		UsageRatedPokemonRepository $usageRatedPokemonRepository,
		UsageExtractor $usageExtractor
	) {
		$this->pokemonRepository = $pokemonRepository;
		$this->usageRepository = $usageRepository;
		$this->usageRatedRepository = $usageRatedRepository;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
		$this->usageExtractor = $usageExtractor;
	}

	/**
	 * Import usage data from the given file.
	 *
	 * @param resource $file
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function importFile(
		resource $file,
		int $year,
		int $month,
		int $formatId,
		int $rating
	) {
		$usageExists = $this->usageRepository->exists(
			$year,
			$month,
			$formatId
		);
		$usageRatedExists = $this->usageRatedRepository->exists(
			$year,
			$month,
			$formatId,
			$rating
		);
		$usagePokemonExists = $this->usagePokemonRepository->exists(
			$year,
			$month,
			$formatId
		);
		$usageRatedPokemonExists = $this->usageRatedPokemonRepository->exists(
			$year,
			$month,
			$formatId,
			$rating
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if (
			$usageExists
			&& $usageRatedExists
			&& $usagePokemonExists
			&& $usageRatedPokemonExists
		) {
			return;
		}

		$line = fgets($line);
		$totalBattles = $this->usageExtractor->extractTotalBattles($line);
		if (!$usageExists) {
			$this->usageRepository->insert(
				$year,
				$month,
				$formatId,
				$totalBattles
			);
		}

		$line = fgets($line);
		$averageWeightPerTeam = $this->usageExtractor->extractAverageWeightPerTeam($line);
		if (!$usageRatedExists) {
			$this->usageRatedRepository->insert(
				$year,
				$month,
				$formatId,
				$rating,
				$averageWeightPerTeam
			);
		}

		// Ignore the next three lines.
		fgets($file);
		fgets($file);
		fgets($file);

		while ($line = fgets($file)) {
			try {
				$usage = $this->usageExtractor->extract($line);

				$pokemonName = $usage->pokemonName();
				$pokemonId = $this->pokemonRepository->getPokemonId($pokemonName);
	
				if (!$usagePokemonExists) {
					$this->usagePokemonRepository->insert(
						$year,
						$month,
						$formatId,
						$pokemonId,
						$usage->raw(),
						$usage->rawPercent(),
						$usage->real(),
						$usage->realPercent()
					);
				}
	
				if (!$usageRatedPokemonExists) {
					$this->usageRatedPokemonRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$usage->rank(),
						$usage->usagePercent()
					);
				}
			} catch (Exception $e) {
				return;
			}
		}
	}
}
