<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use Jp\Dex\Stats\Importers\Extractors\UsageFileExtractor;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Jp\Dex\Stats\Repositories\Usage\UsagePokemonRepository;
use Jp\Dex\Stats\Repositories\Usage\UsageRatedPokemonRepository;
use Jp\Dex\Stats\Repositories\Usage\UsageRatedRepository;
use Jp\Dex\Stats\Repositories\Usage\UsageRepository;
use Psr\Http\Message\StreamInterface;

class UsageFileImporter
{
	/** @var ShowdownPokemonRepository $showdownPokemonRepository */
	protected $showdownPokemonRepository;

	/** @var UsageRepository $usageRepository */
	protected $usageRepository;

	/** @var UsageRatedRepository $usageRatedRepository */
	protected $usageRatedRepository;

	/** @var UsagePokemonRepository $usagePokemonRepository */
	protected $usagePokemonRepository;

	/** @var UsageRatedPokemonRepository $usageRatedPokemonRepository */
	protected $usageRatedPokemonRepository;

	/** @var UsageFileExtractor $usageFileExtractor */
	protected $usageFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param UsageRepository $usageRepository
	 * @param UsageRatedRepository $usageRatedRepository
	 * @param UsagePokemonRepository $usagePokemonRepository
	 * @param UsageRatedPokemonRepository $usageRatedPokemonRepository
	 * @param UsageFileExtractor $usageFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepository $showdownPokemonRepository,
		UsageRepository $usageRepository,
		UsageRatedRepository $usageRatedRepository,
		UsagePokemonRepository $usagePokemonRepository,
		UsageRatedPokemonRepository $usageRatedPokemonRepository,
		UsageFileExtractor $usageFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->usageRepository = $usageRepository;
		$this->usageRatedRepository = $usageRatedRepository;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
		$this->usageFileExtractor = $usageFileExtractor;
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
	) : void {
		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

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
		$totalBattles = $this->usageFileExtractor->extractTotalBattles($line);
		if (!$usageExists) {
			$this->usageRepository->insert(
				$year,
				$month,
				$formatId,
				$totalBattles
			);
		}

		$line = \GuzzleHttp\Psr7\readline($stream);
		$averageWeightPerTeam = $this->usageFileExtractor->extractAverageWeightPerTeam($line);
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

		while ($this->usageFileExtractor->isUsage($line = \GuzzleHttp\Psr7\readline($stream))) {
			$usage = $this->usageFileExtractor->extractUsage($line);
			$showdownPokemonName = $usage->showdownPokemonName();

			// If this Pokémon is not meant to be imported, skip it.
			if (!$this->showdownPokemonRepository->isImported($showdownPokemonName)) {
				continue;
			}

			$pokemonId = $this->showdownPokemonRepository->getPokemonId($showdownPokemonName);

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
		}
	}
}
