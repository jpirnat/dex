<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;

class TeammateModel
{
	/** @var MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository */
	private $movesetRatedTeammateRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

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
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param FormIconRepositoryInterface $formIconRepository
	 */
	public function __construct(
		MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonRepositoryInterface $pokemonRepository,
		FormIconRepositoryInterface $formIconRepository
	) {
		$this->movesetRatedTeammateRepository = $movesetRatedTeammateRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->formIconRepository = $formIconRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		int $year,
		int $month,
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated teammate records.
		$movesetRatedTeammates = $this->movesetRatedTeammateRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
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
