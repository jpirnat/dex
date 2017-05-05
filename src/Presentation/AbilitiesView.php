<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\AbilitiesModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class AbilitiesView
{
	/** @var AbilitiesModel $abilitiesModel */
	private $abilitiesModel;

	/**
	 * Constructor.
	 *
	 * @param AbilitiesModel $abilitiesModel
	 */
	public function __construct(
		AbilitiesModel $abilitiesModel
	) {
		$this->abilitiesModel = $abilitiesModel;
	}

	/**
	 * Get the ability usage history.
	 *
	 * @return ResponseInterface
	 */
	public function getUsage() : ResponseInterface
	{
		$movesetRatedAbilities = $this->abilitiesModel->getUsage();

		$data = [];
		foreach ($movesetRatedAbilities as $movesetRatedAbility) {
			$data[] = [
				'year' => $movesetRatedAbility->year(),
				'month' => $movesetRatedAbility->month(),
				'formatId' => $movesetRatedAbility->formatId()->value(),
				'rating' => $movesetRatedAbility->rating(),
				'pokemonId' => $movesetRatedAbility->pokemonId()->value(),
				'abilityId' => $movesetRatedAbility->abilityId()->value(),
				'percent' => $movesetRatedAbility->percent(),
			];
		}

		$response = new JsonResponse($data);

		return $response;
	}
}
