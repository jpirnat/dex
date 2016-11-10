<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers;

use Exception;
use Jp\Trendalyzer\Importers\Extractors\UsageExtractor;
use Jp\Trendalyzer\Repositories\PokemonRepository;
use Jp\Trendalyzer\Repositories\Usage\UsagePokemonRepository;
use Jp\Trendalyzer\Repositories\Usage\UsageRatedPokemonRepository;
use Jp\Trendalyzer\Repositories\Usage\UsageRatedRepository;
use Jp\Trendalyzer\Repositories\Usage\UsageRepository;
use Psr\Http\Message\StreamInterface;

class UsageFileImporter
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
	 * @param StreamInterface $stream
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function import(
		StreamInterface $stream,
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

		$line = \GuzzleHttp\Psr7\readline($stream);
		$totalBattles = $this->usageExtractor->extractTotalBattles($line);
		if (!$usageExists) {
			$this->usageRepository->insert(
				$year,
				$month,
				$formatId,
				$totalBattles
			);
		}

		$line = \GuzzleHttp\Psr7\readline($stream);
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
		\GuzzleHttp\Psr7\readline($stream);
		\GuzzleHttp\Psr7\readline($stream);
		\GuzzleHttp\Psr7\readline($stream);

		while ($line = \GuzzleHttp\Psr7\readline($stream)) {
			try {
				$usage = $this->usageExtractor->extractUsage($line);

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
