<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\LeadsModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class LeadsView
{
	/** @var LeadsModel $leadsModel */
	private $leadsModel;

	/**
	 * Constructor.
	 *
	 * @param LeadsModel $leadsModel
	 */
	public function __construct(
		LeadsModel $leadsModel
	) {
		$this->leadsModel = $leadsModel;
	}

	/**
	 * Get the usage history of the requested Pokémon in the requested format
	 * across all ratings.
	 *
	 * @return ResponseInterface
	 */
	public function getUsage() : ResponseInterface
	{
		$leadsRatedPokemons = $this->leadsModel->getUsage();

		$data = [];
		foreach ($leadsRatedPokemons as $leadsRatedPokemon) {
			$data[] = [
				'year' => $leadsRatedPokemon->year(),
				'month' => $leadsRatedPokemon->month(),
				'rating' => $leadsRatedPokemon->rating(),
				'percent' => $leadsRatedPokemon->usagePercent(),
			];
		}

		$response = new JsonResponse($data);

		return $response;
	}
}
