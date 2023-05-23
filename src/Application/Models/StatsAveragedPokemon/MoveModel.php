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

final class MoveModel
{
	public function __construct(
		private MovesetRatedAveragedMoveRepositoryInterface $movesetRatedAveragedMoveRepository,
		private MoveNameRepositoryInterface $moveNameRepository,
		private MoveRepositoryInterface $moveRepository,
	) {}

	/**
	 * Set individual Pokémon move data averaged over multiple months.
	 */
	public function setData(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		// Get moveset rated averaged move records for these months.
		$movesetRatedAveragedMoves = $this->movesetRatedAveragedMoveRepository->getByMonthsAndFormatAndRatingAndPokemon(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId
		);

		$moves = [];

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

			$moves[] = [
				'identifier' => $move->getIdentifier(),
				'name' => $moveName->getName(),
				'percent' => $movesetRatedAveragedMove->getPercent(),
			];
		}

		return $moves;
	}
}
