<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;

class MovesModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	protected $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	protected $pokemonRepository;

	/** @var MoveRepositoryInterface $moveRepository */
	protected $moveRepository;

	/** @var MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository */
	protected $movesetRatedMoveRepository;

	/** @var MovesetRatedMove[] $movesetRatedMoves */
	protected $movesetRatedMoves = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param MoveRepositoryInterface $moveRepository
	 * @param MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		MoveRepositoryInterface $moveRepository,
		MovesetRatedMoveRepositoryInterface $movesetRatedMoveRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->moveRepository = $moveRepository;
		$this->movesetRatedMoveRepository = $movesetRatedMoveRepository;
	}


	/**
	 * Set the move usage history of the requested Pokémon in the requested
	 * format for the requested rating.
	 *
	 * @param string $formatIdentifier
	 * @param int $rating
	 * @param string $pokemonIdentifier
	 *
	 * @return void
	 */
	public function setRatingUsage(
		string $formatIdentifier,
		int $rating,
		string $pokemonIdentifier
	) : void {
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);

		$this->movesetRatedMoves = $this->movesetRatedMoveRepository->getByFormatAndRatingAndPokemon(
			$format->id(),
			$rating,
			$pokemon->id()
		);
	}

	/**
	 * Set the move usage history of the requested Pokémon in the requested
	 * format for the requested move across all ratings.
	 *
	 * @param string $formatIdentifier
	 * @param string $pokemonIdentifier
	 * @param string $moveIdentifier
	 *
	 * @return void
	 */
	public function setMoveUsage(
		string $formatIdentifier,
		string $pokemonIdentifier,
		string $moveIdentifier
	) : void {
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		$this->movesetRatedMoves = $this->movesetRatedMoveRepository->getByFormatAndPokemonAndMove(
			$format->id(),
			$pokemon->id(),
			$move->id()
		);
	}

	/**
	 * Get the move usage history.
	 *
	 * @return MovesetRatedMove[]
	 */
	public function getUsage() : array
	{
		return $this->movesetRatedMoves;
	}
}
