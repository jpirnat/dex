<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\ItemsModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

class ItemsView
{
	/** @var ItemsModel $itemsModel */
	private $itemsModel;

	/**
	 * Constructor.
	 *
	 * @param ItemsModel $itemsModel
	 */
	public function __construct(
		ItemsModel $itemsModel
	) {
		$this->itemsModel = $itemsModel;
	}

	/**
	 * Get the item usage history.
	 *
	 * @return ResponseInterface
	 */
	public function getUsage() : ResponseInterface
	{
		$movesetRatedItems = $this->itemsModel->getUsage();

		$data = [];
		foreach ($movesetRatedItems as $movesetRatedItem) {
			$data[] = [
				'year' => $movesetRatedItem->getYear(),
				'month' => $movesetRatedItem->getMonth(),
				'formatId' => $movesetRatedItem->getFormatId()->value(),
				'rating' => $movesetRatedItem->getRating(),
				'pokemonId' => $movesetRatedItem->getPokemonId()->value(),
				'itemId' => $movesetRatedItem->getItemId()->value(),
				'percent' => $movesetRatedItem->getPercent(),
			];
		}

		$response = new JsonResponse($data);

		return $response;
	}
}
