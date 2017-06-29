<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;

class MoveModel
{
	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	private $movesetRatedMoveRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var MoveData[] $moveDatas */
	private $moveDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 */
	public function __construct(
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		MoveNameRepositoryInterface $moveNameRepository
	) {
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
		$this->moveNameRepository = $moveNameRepository;
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
		$movesetRatedMoves = $this->movesetRatedMoveRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$year,
			$month,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each move's data.
		foreach ($movesetRatedMoves as $movesetRatedMove) {
			// Get this move's name.
			$moveName = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$movesetRatedMove->getMoveId()
			);

			$this->moveDatas[] = new MoveData(
				$moveName->getName(),
				$movesetRatedMove->getPercent()
			);
		}
	}

	/**
	 * Get the move datas.
	 *
	 * @return MoveData[]
	 */
	public function getMoveDatas() : array
	{
		return $this->moveDatas;
	}
}
