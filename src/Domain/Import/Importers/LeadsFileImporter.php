<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Import\Importers;

use DateTime;
use GuzzleHttp\Psr7\Utils;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Import\Extractors\LeadsFileExtractor;
use Jp\Dex\Domain\Import\Showdown\ShowdownPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Leads;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\LeadsRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use Psr\Http\Message\StreamInterface;

final readonly class LeadsFileImporter
{
	public function __construct(
		private ShowdownPokemonRepositoryInterface $showdownPokemonRepository,
		private LeadsRepositoryInterface $leadsRepository,
		private UsageRatedPokemonRepositoryInterface $usageRatedPokemonRepository,
		private LeadsPokemonRepositoryInterface $leadsPokemonRepository,
		private LeadsRatedPokemonRepositoryInterface $leadsRatedPokemonRepository,
		private LeadsFileExtractor $leadsFileExtractor,
	) {}

	/**
	 * Import leads data from the given file.
	 */
	public function import(
		StreamInterface $stream,
		DateTime $month,
		FormatId $formatId,
		int $rating,
	) : void {
		$now = new DateTime()->format('Y-m-d H:i:s');
		echo 'Importing leads file: month ' . $month->format('Y-m')
			. ', format id ' . $formatId->value()
			. ", rating $rating. ($now)\n";

		// If the file is empty, there's nothing to import.
		if ($stream->getSize() === 0) {
			return;
		}

		$leadsExists = $this->leadsRepository->has(
			$month,
			$formatId,
		);
		$leadsPokemonExists = $this->leadsPokemonRepository->hasAny(
			$month,
			$formatId,
		);
		$leadsRatedPokemonExists = $this->leadsRatedPokemonRepository->hasAny(
			$month,
			$formatId,
			$rating,
		);

		// If all data in this file has already been imported, there's no need
		// to import it again. We can quit early.
		if ($leadsExists
			&& $leadsPokemonExists
			&& $leadsRatedPokemonExists
		) {
			return;
		}

		$line = Utils::readLine($stream);
		$totalLeads = $this->leadsFileExtractor->extractTotalLeads($line);
		if (!$leadsExists) {
			$leads = new Leads(
				$month,
				$formatId,
				$totalLeads,
			);
			$this->leadsRepository->save($leads);
		}

		// Ignore the next three lines.
		Utils::readLine($stream);
		Utils::readLine($stream);
		Utils::readLine($stream);

		while ($this->leadsFileExtractor->isLeadUsage($line = Utils::readLine($stream))) {
			$leadUsage = $this->leadsFileExtractor->extractLeadUsage($line);
			$showdownPokemonName = $leadUsage->showdownPokemonName;

			// If this Pokémon is not meant to be imported, skip it.
			if (!$this->showdownPokemonRepository->isImported($showdownPokemonName)) {
				continue;
			}

			$pokemonId = $this->showdownPokemonRepository->getPokemonId($showdownPokemonName);
			$usageRatedPokemonId = $this->usageRatedPokemonRepository->getId(
				$month,
				$formatId,
				$rating,
				$pokemonId,
			);
			if (!$usageRatedPokemonId) {
				continue;
			}

			if (!$leadsPokemonExists) {
				$leadsPokemon = new LeadsPokemon(
					$month,
					$formatId,
					$pokemonId,
					$leadUsage->raw,
					$leadUsage->rawPercent,
				);
				$this->leadsPokemonRepository->save($leadsPokemon);
			}

			if (!$leadsRatedPokemonExists) {
				$leadsRatedPokemon = new LeadsRatedPokemon(
					$usageRatedPokemonId,
					$leadUsage->rank,
					$leadUsage->usagePercent,
				);
				$this->leadsRatedPokemonRepository->save($leadsRatedPokemon);
			}
		}
	}
}
