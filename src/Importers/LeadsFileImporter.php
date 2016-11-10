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

class LeadsFileImporter
{
	/** @var PokemonRepository $pokemonRepository */
	protected $pokemonRepository;

	/**
	 * Constructor.
	 *
	 * @param PokemonRepository $pokemonRepository
	 */
	public function __construct(
		PokemonRepository $pokemonRepository
	) {
		$this->pokemonRepository = $pokemonRepository;
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
		// TODO
	}
}
