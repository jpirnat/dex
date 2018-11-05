<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use DateTime;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;

class CounterModel
{
	/** @var MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository */
	private $movesetRatedCounterRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository */
	private $movesetRatedPokemonRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var CounterData[] $counterDatas */
	private $counterDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		MovesetRatedCounterRepositoryInterface $movesetRatedCounterRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->movesetRatedCounterRepository = $movesetRatedCounterRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->movesetRatedPokemonRepository = $movesetRatedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param DateTime $month
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $month,
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated move records.
		$movesetRatedCounters = $this->movesetRatedCounterRepository->getByMonthAndFormatAndRatingAndPokemon(
			$month,
			$format->getId(),
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

			// Does this teammate have moveset rated Pokémon data?
			$movesetDataExists = $this->movesetRatedPokemonRepository->has(
				$month,
				$format->getId(),
				$rating,
				$movesetRatedCounter->getCounterId()
			);

			// Get this counter's Pokémon data.
			$pokemon = $this->pokemonRepository->getById(
				$movesetRatedCounter->getCounterId()
			);

			// Get this counter's form icon.
			$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
				$format->getGenerationId(),
				new FormId($pokemon->getId()->value()), // A Pokémon's default form has Pokémon id === form id.
				false,
				false
			);

			$this->counterDatas[] = new CounterData(
				$pokemonName->getName(),
				$movesetDataExists,
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
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
