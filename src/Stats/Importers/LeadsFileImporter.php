<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Importers;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Stats\Leads\Leads;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use Jp\Dex\Stats\Importers\Extractors\LeadsFileExtractor;
use Jp\Dex\Stats\Repositories\ShowdownPokemonRepository;
use Psr\Http\Message\StreamInterface;

class LeadsFileImporter
{
	/** @var ShowdownPokemonRepository $showdownPokemonRepository */
	protected $showdownPokemonRepository;

	/** @var LeadsRepositoryInterface $leadsRepository */
	protected $leadsRepository;

	/** @var LeadsPokemonRepositoryInterface $leadsPokemonRepository */
	protected $leadsPokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	protected $leadsRatedPokemonRepository;

	/** @var LeadsFileExtractor $leadsFileExtractor */
	protected $leadsFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepository $showdownPokemonRepository
	 * @param LeadsRepositoryInterface $leadsRepository
	 * @param LeadsPokemonRepositoryInterface $leadsPokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param LeadsFileExtractor $leadsFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepository $showdownPokemonRepository,
		LeadsRepositoryInterface $leadsRepository,
		LeadsPokemonRepositoryInterface $leadsPokemonRepository,
		LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		LeadsFileExtractor $leadsFileExtractor
	) {
		$this->showdownPokemonRepository = $showdownPokemonRepository;
		$this->leadsRepository = $leadsRepository;
		$this->leadsPokemonRepository = $leadsPokemonRepository;
		$this->leadsRatedPokemonRepository = $leadsRatedPokemonRepository;
		$this->leadsFileExtractor = $leadsFileExtractor;
	}

	/**
	 * Import leads data from the given file.
	 *
	 * @param StreamInterface $stream
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function import(
		StreamInterface $stream,
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : void {
		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$leadsExists = $this->leadsRepository->has(
			$year,
			$month,
			$formatId
		);
		$leadsPokemonExists = $this->leadsPokemonRepository->has(
			$year,
			$month,
			$formatId
		);
		$leadsRatedPokemonExists = $this->leadsRatedPokemonRepository->has(
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
		$totalLeads = $this->leadsFileExtractor->extractTotalLeads($line);
		if (!$leadsExists) {
			$leads = new Leads(
				$year,
				$month,
				$formatId,
				$totalLeads
			);
			$this->leadsRepository->save($leads);
		}

		// Ignore the next three lines.
		\GuzzleHttp\Psr7\readline($stream);
		\GuzzleHttp\Psr7\readline($stream);
		\GuzzleHttp\Psr7\readline($stream);

		while ($this->leadsFileExtractor->isLeadUsage($line = \GuzzleHttp\Psr7\readline($stream))) {
			$leadUsage = $this->leadsFileExtractor->extractLeadUsage($line);
			$showdownPokemonName = $leadUsage->showdownPokemonName();

			// If this Pokémon is not meant to be imported, skip it.
			if (!$this->showdownPokemonRepository->isImported($showdownPokemonName)) {
				continue;
			}

			$pokemonId = $this->showdownPokemonRepository->getPokemonId($showdownPokemonName);

			if (!$leadsPokemonExists) {
				$leadsPokemon = new LeadsPokemon(
					$year,
					$month,
					$formatId,
					$pokemonId,
					$leadUsage->raw(),
					$leadUsage->rawPercent()
				);
				$this->leadsPokemonRepository->save($leadsPokemon);
			}

			if (!$leadsRatedPokemonExists) {
				$leadsRatedPokemon = new LeadsRatedPokemon(
					$year,
					$month,
					$formatId,
					$rating,
					$pokemonId,
					$leadUsage->rank(),
					$leadUsage->usagePercent()
				);
				$this->leadsRatedPokemonRepository->save($leadsRatedPokemon);
			}
		}
	}
}
