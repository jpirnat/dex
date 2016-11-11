<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Importers;

use Exception;
use Jp\Trendalyzer\Importers\Extractors\LeadUsageExtractor;
use Jp\Trendalyzer\Repositories\PokemonRepository;
use Jp\Trendalyzer\Repositories\Leads\LeadsPokemonRepository;
use Jp\Trendalyzer\Repositories\Leads\LeadsRatedPokemonRepository;
use Jp\Trendalyzer\Repositories\Leads\LeadsRepository;
use Psr\Http\Message\StreamInterface;

class LeadsFileImporter
{
	/** @var PokemonRepository $pokemonRepository */
	protected $pokemonRepository;

	/** @var LeadsRepository $leadsRepository */
	protected $leadsRepository;

	/** @var LeadsPokemonRepository $leadsPokemonRepository */
	protected $leadsPokemonRepository;

	/** @var LeadsRatedPokemonRepository $leadsRatedPokemonRepository */
	protected $leadsRatedPokemonRepository;

	/** @var LeadUsageExtractor $leadUsageExtractor */
	protected $leadUsageExtractor;

	/**
	 * Constructor.
	 *
	 * @param PokemonRepository $pokemonRepository
	 * @param LeadsRepository $leadsRepository
	 * @param LeadsPokemonRepository $leadsPokemonRepository
	 * @param LeadsRatedPokemonRepository $leadsRatedPokemonRepository
	 * @param LeadUsageExtractor $leadUsageExtractor
	 */
	public function __construct(
		PokemonRepository $pokemonRepository,
		LeadsRepository $leadsRepository,
		LeadsPokemonRepository $leadsPokemonRepository,
		LeadsRatedPokemonRepository $leadsRatedPokemonRepository,
		LeadUsageExtractor $leadUsageExtractor
	) {
		$this->pokemonRepository = $pokemonRepository;
		$this->leadsRepository = $leadsRepository;
		$this->leadsPokemonRepository = $leadsPokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->leadUsageExtractor = $leadUsageExtractor;
	}

	/**
	 * Import leads data from the given file.
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
		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$leadsExists = $this->leadsRepository->exists(
			$year,
			$month,
			$formatId
		);
		$leadsPokemonExists = $this->leadsPokemonRepository->exists(
			$year,
			$month,
			$formatId
		);
		$leadsRatedPokemonExists = $this->leadsRatedPokemonRepository->exists(
			$year,
			$month,
			$formatId,
			$rating
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if (
			$leadsExists
			&& $leadsPokemonExists
			&& $leadsRatedPokemonExists
		) {
			return;
		}

		$line = \GuzzleHttp\Psr7\readline($stream);
		$totalLeads = $this->leadUsageExtractor->extractTotalLeads($line);
		if (!$leadsExists) {
			$this->leadsRepository->insert(
				$year,
				$month,
				$formatId,
				$totalLeads
			);
		}

		// Ignore the next three lines.
		\GuzzleHttp\Psr7\readline($stream);
		\GuzzleHttp\Psr7\readline($stream);
		\GuzzleHttp\Psr7\readline($stream);

		while ($line = \GuzzleHttp\Psr7\readline($stream)) {
			try {
				$leadUsage = $this->leadUsageExtractor->extractLeadUsage($line);

				$pokemonName = $leadUsage->pokemonName();
				$pokemonId = $this->pokemonRepository->getPokemonId($pokemonName);
	
				if (!$leadsPokemonExists) {
					$this->leadsPokemonRepository->insert(
						$year,
						$month,
						$formatId,
						$pokemonId,
						$leadUsage->raw(),
						$leadUsage->rawPercent()
					);
				}
	
				if (!$leadsRatedPokemonExists) {
					$this->leadsRatedPokemonRepository->insert(
						$year,
						$month,
						$formatId,
						$rating,
						$pokemonId,
						$leadUsage->rank(),
						$leadUsage->usagePercent()
					);
				}
			} catch (Exception $e) {
				return;
			}
		}
	}
}
