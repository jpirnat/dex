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
				'year' => $movesetRatedAbility->getYear(),
				'month' => $movesetRatedAbility->getMonth(),
				'formatId' => $movesetRatedAbility->getFormatId()->value(),
				'rating' => $movesetRatedAbility->getRating(),
				'pokemonId' => $movesetRatedAbility->getPokemonId()->value(),
				'abilityId' => $movesetRatedAbility->getAbilityId()->value(),
				'percent' => $movesetRatedAbility->getPercent(),
			];
		}

		$response = new JsonResponse($data);

		return $response;
	}
}
