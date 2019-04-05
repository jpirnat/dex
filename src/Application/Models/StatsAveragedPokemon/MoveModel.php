<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsAveragedPokemon;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedMoveRepositoryInterface;

class MoveModel
{
	/** @var MovesetRatedAveragedMoveRepositoryInterface $movesetRatedAveragedMoveRepository */
	private $movesetRatedAveragedMoveRepository;

	/** @var MoveNameRepositoryInterface $moveNameRepository */
	private $moveNameRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	private $moveRepository;


	/** @var MoveData[] $moveDatas */
	private $moveDatas = [];


	/**
	 * Constructor.
	 *
	 * @param MovesetRatedAveragedMoveRepositoryInterface $movesetRatedAveragedMoveRepository
	 * @param MoveNameRepositoryInterface $moveNameRepository
	 * @param MoveRepositoryInterface $moveRepository
	 */
	public function __construct(
		MovesetRatedAveragedMoveRepositoryInterface $movesetRatedAveragedMoveRepository,
		MoveNameRepositoryInterface $moveNameRepository,
		MoveRepositoryInterface $moveRepository
	) {
		$this->movesetRatedAveragedMoveRepository = $movesetRatedAveragedMoveRepository;
		$this->moveNameRepository = $moveNameRepository;
		$this->moveRepository = $moveRepository;
	}

	/**
	 * Get moveset data averaged over multiple months.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		// Get moveset rated averaged move records for these months.
		$movesetRatedAveragedMoves = $this->movesetRatedAveragedMoveRepository->getByMonthsAndFormatAndRatingAndPokemon(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId
		);

		// Get each move's data.
		foreach ($movesetRatedAveragedMoves as $movesetRatedAveragedMove) {
			$moveId = $movesetRatedAveragedMove->getMoveId();

			// Get this move's name.
			$moveName = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				$moveId
			);

			// Get this move.
			$move = $this->moveRepository->getById($moveId);

			$this->moveDatas[] = new MoveData(
				$moveName->getName(),
				$move->getIdentifier(),
				$movesetRatedAveragedMove->getPercent()
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
