<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Import;

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

	/**
	 * Constructor.
	 *
	 * @param PokemonRepository $pokemonRepository
	 * @param UsageRepository $usageRepository
	 * @param UsageRatedRepository $usageRatedRepository
	 * @param UsagePokemonRepository $usagePokemonRepository
	 * @param UsageRatedPokemonRepository $usageRatedPokemonRepository
	 */
	public function __construct(
		PokemonRepository $pokemonRepository,
		UsageRepository $usageRepository,
		UsageRatedRepository $usageRatedRepository,
		UsagePokemonRepository $usagePokemonRepository,
		UsageRatedPokemonRepository $usageRatedPokemonRepository
	) {
		$this->pokemonRepository = $pokemonRepository;
		$this->usageRepository = $usageRepository;
		$this->usageRatedRepository = $usageRatedRepository;
		$this->usagePokemonRepository = $usagePokemonRepository;
		$this->usageRatedPokemonRepository = $usageRatedPokemonRepository;
	}

	/**
	 * Import usage data from the given file.
	 *
	 * @param resource $file
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $rating
	 *
	 * @return void
	 */
	public function importFile(
		resource $file,
		int $year,
		int $month,
		int $metagameId,
		int $rating
	) {
		$usageExists = $this->usageRepository->exists(
			$year,
			$month,
			$metagameId
		);
		$usageRatedExists = $this->usageRatedRepository->exists(
			$year,
			$month,
			$metagameId,
			$rating
		);
		$usagePokemonExists = $this->usagePokemonRepository->exists(
			$year,
			$month,
			$metagameId
		);
		$usageRatedPokemonExists = $this->usageRatedPokemonRepository->exists(
			$year,
			$month,
			$metagameId,
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

		// TODO
	}
}
