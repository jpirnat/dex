<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\MovesModel;
use Psr\Http\Message\ServerRequestInterface;

class MovesController
{
	/** @var MovesModel $movesModel */
	private $movesModel;

	/**
	 * Constructor.
	 *
	 * @param MovesModel $movesModel
	 */
	public function __construct(
		MovesModel $movesModel
	) {
		$this->movesModel = $movesModel;
	}

	/**
	 * Get the move usage history of the requested Pokémon in the requested
	 * format for the requested rating.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setRatingUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');

		$this->movesModel->setRatingUsage(
			$formatIdentifier,
			$rating,
			$pokemonIdentifier
		);
	}

	/**
	 * Get the move usage history of the requested Pokémon in the requested
	 * format for the requested move across all ratings.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setMoveUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$pokemonIdentifier = $request->getAttribute('pokemonIdentifier');
		$moveIdentifier = $request->getAttribute('move_identifier');

		$this->movesModel->setMoveUsage(
			$formatIdentifier,
			$pokemonIdentifier,
			$moveIdentifier
		);
	}
}
