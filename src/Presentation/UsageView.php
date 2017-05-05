<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\UsageModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class UsageView
{
	/** @var UsageModel $usageModel */
	private $usageModel;

	/**
	 * Constructor.
	 *
	 * @param UsageModel $usageModel
	 */
	public function __construct(
		UsageModel $usageModel
	) {
		$this->usageModel = $usageModel;
	}

	/**
	 * Get the usage history of the requested Pokémon in the requested format
	 * across all ratings.
	 *
	 * @return ResponseInterface
	 */
	public function getUsage() : ResponseInterface
	{
		$usageRatedPokemons = $this->usageModel->getUsage();

		$data = [];
		foreach ($usageRatedPokemons as $usageRatedPokemon) {
			$data[] = [
				'year' => $usageRatedPokemon->year(),
				'month' => $usageRatedPokemon->month(),
				'rating' => $usageRatedPokemon->rating(),
				'percent' => $usageRatedPokemon->usagePercent(),
			];
		}

		$response = new JsonResponse($data);

		return $response;
	}
}
