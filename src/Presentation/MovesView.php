<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\MovesModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class MovesView
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
	 * Get the move usage history.
	 *
	 * @return ResponseInterface
	 */
	public function getUsage() : ResponseInterface
	{
		$movesetRatedMoves = $this->movesModel->getUsage();

		$data = [];
		foreach ($movesetRatedMoves as $movesetRatedMove) {
			$data[] = [
				'year' => $movesetRatedMove->getYear(),
				'month' => $movesetRatedMove->getMonth(),
				'formatId' => $movesetRatedMove->getFormatId()->value(),
				'rating' => $movesetRatedMove->getRating(),
				'pokemonId' => $movesetRatedMove->getPokemonId()->value(),
				'moveId' => $movesetRatedMove->getMoveId()->value(),
				'percent' => $movesetRatedMove->getPercent(),
			];
		}

		$response = new JsonResponse($data);

		return $response;
	}
}
