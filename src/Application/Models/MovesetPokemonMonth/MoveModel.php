<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\MovesetPokemonMonth;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use Jp\Dex\Domain\YearMonth;

class MoveModel
{
	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	private $movesetRatedMoveRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;

	/** @var MoveData[] $moveDatas */
	private $moveDatas = [];

	/**
	 * Constructor.
	 *
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param MoveRepositoryInterface $moveRepository
	 */
	public function __construct(
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		MoveRepositoryInterface $moveRepository
	) {
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->moveRepository = $moveRepository;
	}

	/**
	 * Get moveset data to recreate a stats moveset file, such as
	 * http://www.smogon.com/stats/2014-11/moveset/ou-1695.txt, for a single Pokémon.
	 *
	 * @param YearMonth $thisMonth
	 * @param YearMonth $prevMonth
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		YearMonth $thisMonth,
		YearMonth $prevMonth,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated move records for this month.
		$movesetRatedMoves = $this->movesetRatedMoveRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$thisMonth->getYear(),
			$thisMonth->getMonth(),
			$formatId,
			$rating,
			$pokemonId
		);

		// Get moveset rated move records for the previous month.
		$prevMonthMoves = $this->movesetRatedMoveRepository->getByYearAndMonthAndFormatAndRatingAndPokemon(
			$prevMonth->getYear(),
			$prevMonth->getMonth(),
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each move's data.
		foreach ($movesetRatedMoves as $movesetRatedMove) {
			$moveId = $movesetRatedMove->getMoveId();

			// Get this move's name.
			$moveName = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$moveId
			);

			// Get this move.
			$move = $this->moveRepository->getById($moveId);

			// Get this move's percent from the previous month.
			if (isset($prevMonthMoves[$moveId->value()])) {
				$change = $movesetRatedMove->getPercent() - $prevMonthMoves[$moveId->value()]->getPercent();
			} else {
				$change = $movesetRatedMove->getPercent();
			}

			$this->moveDatas[] = new MoveData(
				$moveName->getName(),
				$move->getIdentifier(),
				$movesetRatedMove->getPercent(),
				$change
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
