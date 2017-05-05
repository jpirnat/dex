<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\ItemsModel;
use Psr\Http\Message\ServerRequestInterface;

class ItemsController
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
	 * Get the item usage history of the requested Pokémon in the requested
	 * format for the requested rating.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setRatingUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('format_identifier');
		$rating = (int) $request->getAttribute('rating');
		$pokemonIdentifier = $request->getAttribute('pokemon_identifier');

		$this->itemsModel->setRatingUsage(
			$formatIdentifier,
			$rating,
			$pokemonIdentifier
		);
	}

	/**
	 * Get the item usage history of the requested Pokémon in the requested
	 * format for the requested item across all ratings.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setItemUsage(ServerRequestInterface $request) : void
	{
		$formatIdentifier = $request->getAttribute('format_identifier');
		$pokemonIdentifier = $request->getAttribute('pokemon_identifier');
		$itemIdentifier = $request->getAttribute('item_identifier');

		$this->itemsModel->setItemUsage(
			$formatIdentifier,
			$pokemonIdentifier,
			$itemIdentifier
		);
	}
}
