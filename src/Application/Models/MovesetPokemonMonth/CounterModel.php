<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;

class CounterModel
{
	/** @var MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository */
	private $movesetRatedCounterRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var CounterData[] $counterDatas */
	private $counterDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 */
	public function __construct(
		MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonRepositoryInterface $pokemonRepository
	) {
		$this->movesetRatedCounterRepository = $movesetRatedCounterRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->pokemonRepository = $pokemonRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated move records.
		$movesetRatedCounters = $this->movesetRatedCounterRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each counter's data.
		foreach ($movesetRatedCounters as $movesetRatedCounter) {
			// Get this counter's name.
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$movesetRatedCounter->getCounterId()
			);

			// Get this counter's Pokémon data.
			$pokemon = $this->pokemonRepository->getById(
				$movesetRatedCounter->getCounterId()
			);

			$this->counterDatas[] = new CounterData(
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$movesetRatedCounter->getNumber1(),
				$movesetRatedCounter->getNumber2(),
				$movesetRatedCounter->getNumber3(),
				$movesetRatedCounter->getPercentKnockedOut(),
				$movesetRatedCounter->getPercentSwitchedOut()
			);
		}
	}

	/**
	 * Get the counter datas.
	 *
	 * @return CounterData[]
	 */
	public function getCounterDatas() : array
	{
		return $this->counterDatas;
	}
}
