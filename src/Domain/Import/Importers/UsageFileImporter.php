<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Import\Extractors\UsageFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Usage;
use Jp\Dex\Domain\Stats\Usage\UsagePokemon;
use Jp\Dex\Domain\Stats\Usage\UsagePokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRated;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final class UsageFileImporter
{
	private ShowdownPokemonRepositoryInterface $showdownPokemonRepository;
	private UsageRepositoryInterface $usageRepository;
	private UsageRatedRepositoryInterface $usageRatedRepository;
	private UsagePokemonRepositoryInterface $usagePokemonRepository;
	private UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository;
	private UsageFileExtractor $usageFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepositoryInterface $showdownPokemonRepository
	 * @param UsageRepositoryInterface $usageRepository
	 * @param UsageRatedRepositoryInterface $usageRatedRepository
	 * @param UsagePokemonRepositoryInterface $usagePokemonRepository
	 * @param UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository
	 * @param UsageFileExtractor $usageFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		UsageRepositoryInterface $usageRepository,
		UsageRatedRepositoryInterface $usageRatedRepository,
		UsagePokemonRepositoryInterface $usagePokemonRepository,
		UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
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
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function import(
		StreamInterface $stream,
		DateTime $month,
		FormatId $formatId,
		int $rating
	) : void {
		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$usageExists = $this->usageRepository->has(
			$month,
			$formatId
		);
		$usageRatedExists = $this->usageRatedRepository->has(
			$month,
			$formatId,
			$rating
		);
		$usagePokemonExists = $this->usagePokemonRepository->hasAny(
			$month,
			$formatId
		);
		$usageRatedPokemonExists = $this->usageRatedPokemonRepository->hasAny(
			$month,
			$formatId,
			$rating
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if ($usageExists
			&& $usageRatedExists
			&& $usagePokemonExists
			&& $usageRatedPokemonExists
		) {
			return;
		}

		$line = \GuzzleHttp\Psr7\readline($stream);
		$totalBattles = $this->usageFileExtractor->extractTotalBattles($line);
		if (!$usageExists) {
			$usage = new Usage(
				$month,
				$formatId,
				$totalBattles
			);
			$this->usageRepository->save($usage);
		}

		$line = \GuzzleHttp\Psr7\readline($stream);
		$averageWeightPerTeam = $this->usageFileExtractor->extractAverageWeightPerTeam($line);
		if (!$usageRatedExists) {
			$usageRated = new UsageRated(
				$month,
				$formatId,
				$rating,
				$averageWeightPerTeam
			);
			$this->usageRatedRepository->save($usageRated);
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
				$usagePokemon = new UsagePokemon(
					$month,
					$formatId,
					$pokemonId,
					$usage->raw(),
					$usage->rawPercent(),
					$usage->real(),
					$usage->realPercent()
				);
				$this->usagePokemonRepository->save($usagePokemon);
			}

			if (!$usageRatedPokemonExists) {
				$usageRatedPokemon = new UsageRatedPokemon(
					$month,
					$formatId,
					$rating,
					$pokemonId,
					$usage->rank(),
					$usage->usagePercent()
				);
				$this->usageRatedPokemonRepository->save($usageRatedPokemon);
			}
		}
	}
}
