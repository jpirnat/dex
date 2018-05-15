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
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;

class TeammateModel
{
	/** @var MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository */
	private $movesetRatedTeammateRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository */
	private $movesetRatedPokemonRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var TeammateData[] $teammateDatas */
	private $teammateDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		MovesetRatedPokemonRepositoryInterface $movesetRatedPokemonRepository,
		PokemonRepositoryInterface $pokemonRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->movesetRatedTeammateRepository = $movesetRatedTeammateRepository;
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
		// Get moveset rated teammate records.
		$movesetRatedTeammates = $this->movesetRatedTeammateRepository->getByMonthAndFormatAndRatingAndPokemon(
			$month,
			$format->getId(),
			$rating,
			$pokemonId
		);

		// Get each teammate's data.
		foreach ($movesetRatedTeammates as $movesetRatedTeammate) {
			// Get this teammate's name.
			$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
				$languageId,
				$movesetRatedTeammate->getTeammateId()
			);

			// Does this teammate have moveset rated Pokémon data?
			$movesetDataExists = $this->movesetRatedPokemonRepository->has(
				$month,
				$format->getId(),
				$rating,
				$movesetRatedTeammate->getTeammateId()
			);

			// Get this teammate's Pokémon data.
			$pokemon = $this->pokemonRepository->getById(
				$movesetRatedTeammate->getTeammateId()
			);

			// Get this teammate's form icon.
			$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
				$format->getGeneration(),
				new FormId($pokemon->getId()->value()), // A Pokémon's default form has Pokémon id === form id.
				false,
				false
			);

			$this->teammateDatas[] = new TeammateData(
				$pokemonName->getName(),
				$movesetDataExists,
				$pokemon->getIdentifier(),
				$formIcon->getImage(),
				$movesetRatedTeammate->getPercent()
			);
		}
	}

	/**
	 * Get the teammate datas.
	 *
	 * @return TeammateData[]
	 */
	public function getTeammateDatas() : array
	{
		return $this->teammateDatas;
	}
}
