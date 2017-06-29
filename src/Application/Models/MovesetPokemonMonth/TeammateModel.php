<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\FormatId;
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

	/** @var TeammateData[] $teammateDatas */
	private $teammateDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 */
	public function __construct(
		MovesetRatedTeammateRepositoryInterface $movesetRatedTeammateRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonRepositoryInterface $pokemonRepository
	) {
		$this->movesetRatedTeammateRepository = $movesetRatedTeammateRepository;
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
		// Get moveset rated teammate records.
		$movesetRatedTeammates = $this->movesetRatedTeammateRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$formatId,
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

			$this->teammateDatas[] = new TeammateData(
				$pokemonName->getName(),
				$pokemon->getIdentifier(),
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
