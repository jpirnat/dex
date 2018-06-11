<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\LeadsAveraged;

use DateTime;
use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;

class LeadsAveragedModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	private $formatRepository;

	/** @var LeadsAveragedPokemonRepositoryInterface $leadsAveragedPokemonRepository */
	private $leadsAveragedPokemonRepository;

	/** @var LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository */
	private $leadsRatedAveragedPokemonRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	// When I figure out how to decide whether a Pokémon's moveset link should
	// be showed, that dependency will go here.

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;


	/** @var string $start */
	private $start;

	/** @var string $end */
	private $end;

	/** @var string $formatIdentifier */
	private $formatIdentifier;

	/** @var int $rating */
	private $rating;

	/** @var LanguageId $languageId */
	private $languageId;

	/** @var LeadsData[] $leadsDatas */
	private $leadsDatas = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param LeadsAveragedPokemonRepositoryInterface $leadsAveragedPokemonRepository
	 * @param LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		LeadsAveragedPokemonRepositoryInterface $leadsAveragedPokemonRepository,
		LeadsRatedAveragedPokemonRepositoryInterface $leadsRatedAveragedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->leadsAveragedPokemonRepository = $leadsAveragedPokemonRepository;
		$this->leadsRatedAveragedPokemonRepository = $leadsRatedAveragedPokemonRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get leads data averaged over multiple months.
	 *
	 * @param string $start
	 * @param string $end
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $start,
		string $end,
		string $formatIdentifier,
		int $rating,
		LanguageId $languageId
	) : void {
		$this->start = $start;
		$this->end = $end;
		$this->formatIdentifier = $formatIdentifier;
		$this->rating = $rating;
		$this->languageId = $languageId;

		// Get the start month and end month.
		$start = new DateTime("$start-01");
		$end = new DateTime("$end-01");

		// Get the format.
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);

		// Get leads Pokémon records for this month.
		$leadsAveragedPokemons = $this->leadsAveragedPokemonRepository->getByMonthsAndFormat(
			$start,
			$end,
			$format->getId()
		);

		// Get leads rated Pokémon records for this month.
		$leadsRatedAveragedPokemons = $this->leadsRatedAveragedPokemonRepository->getByMonthsAndFormatAndRating(
			$start,
			$end,
			$format->getId(),
			$rating
		);

		// Get Pokémon.
		$pokemons = $this->pokemonRepository->getAll();

		// Get Pokémon names.
		$pokemonNames = $this->pokemonNameRepository->getByLanguage($languageId);

		// Get form icons.
		$formIcons = $this->formIconRepository->getByGenerationAndFemaleAndRight(
			$format->getGeneration(),
			false,
			false
		);

		// Get each leads record's data.
		foreach ($leadsRatedAveragedPokemons as $leadsRatedAveragedPokemon) {
			$pokemonId = $leadsRatedAveragedPokemon->getPokemonId();

			// Get this Pokémon's name.
			$pokemonName = $pokemonNames[$pokemonId->value()];

			// Get this Pokémon.
			$pokemon = $pokemons[$pokemonId->value()];

			// Get this Pokémon's form icon.
			$formIcon = $formIcons[$pokemonId->value()]; // A Pokémon's default form has Pokémon id === form id.

			// Get this Pokémon's non-rated usage record for this month.
			$leadsAveragedPokemon = $leadsAveragedPokemons[$pokemonId->value()];

			$this->leadsDatas[] = new LeadsData(
				$leadsRatedAveragedPokemon->getRank(),
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$leadsRatedAveragedPokemon->getUsagePercent(),
				$leadsAveragedPokemon->getRaw(),
				$leadsAveragedPokemon->getRawPercent()
			);
		}
	}

	/**
	 * Get the start month.
	 *
	 * @return string
	 */
	public function getStart() : string
	{
		return $this->start;
	}

	/**
	 * Get the end month.
	 *
	 * @return string
	 */
	public function getEnd() : string
	{
		return $this->end;
	}

	/**
	 * Get the format identifier.
	 *
	 * @return string
	 */
	public function getFormatIdentifier() : string
	{
		return $this->formatIdentifier;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the language id.
	 *
	 * @return LanguageId
	 */
	public function getLanguageId() : LanguageId
	{
		return $this->languageId;
	}

	/**
	 * Get the leads datas.
	 *
	 * @return LeadsData[]
	 */
	public function getLeadsDatas() : array
	{
		return $this->leadsDatas;
	}
}
