<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Import\Extractors\LeadsFileExtractor;
use Jp\Dex\Domain\Stats\Leads\Leads;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use Jp\Dex\Domain\Stats\Showdown\ShowdownPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

class LeadsFileImporter
{
	/** @var ShowdownPokemonRepositoryInterface $showdownPokemonRepository */
	private $showdownPokemonRepository;

	/** @var LeadsRepositoryInterface $leadsRepository */
	private $leadsRepository;

	/** @var LeadsPokemonRepositoryInterface $leadsPokemonRepository */
	private $leadsPokemonRepository;

	/** @var LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository */
	private $leadsRatedPokemonRepository;

	/** @var LeadsFileExtractor $leadsFileExtractor */
	private $leadsFileExtractor;

	/**
	 * Constructor.
	 *
	 * @param ShowdownPokemonRepositoryInterface $showdownPokemonRepository
	 * @param LeadsRepositoryInterface $leadsRepository
	 * @param LeadsPokemonRepositoryInterface $leadsPokemonRepository
	 * @param LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository
	 * @param LeadsFileExtractor $leadsFileExtractor
	 */
	public function __construct(
		ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
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

		$leadsExists = $this->leadsRepository->has(
			$month,
			$formatId
		);
		$leadsPokemonExists = $this->leadsPokemonRepository->hasAny(
			$month,
			$formatId
		);
		$leadsRatedPokemonExists = $this->leadsRatedPokemonRepository->hasAny(
			$month,
			$formatId,
			$rating
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if ($leadsExists
			&& $leadsPokemonExists
			&& $leadsRatedPokemonExists
		) {
			return;
		}

		$line = \GuzzleHttp\Psr7\readline($stream);
		$totalLeads = $this->leadsFileExtractor->extractTotalLeads($line);
		if (!$leadsExists) {
			$leads = new Leads(
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
