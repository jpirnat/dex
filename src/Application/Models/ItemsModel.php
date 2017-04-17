<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Formats\FormatRepositoryInterface;
use Jp\Dex\Domain\Items\ItemRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;

class ItemsModel
{
	/** @var FormatRepositoryInterface $formatRepository */
	protected $formatRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	protected $pokemonRepository;

	/** @var ItemRepositoryInterface $itemRepository */
	protected $itemRepository;

	/** @var MovesetRatedItemRepositoryInterface $movesetRatedItemRepository */
	protected $movesetRatedItemRepository;

	/** @var MovesetRatedItem[] $movesetRatedItems */
	protected $movesetRatedItems = [];

	/**
	 * Constructor.
	 *
	 * @param FormatRepositoryInterface $formatRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param ItemRepositoryInterface $itemRepository
	 * @param MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	 */
	public function __construct(
		FormatRepositoryInterface $formatRepository,
		PokemonRepositoryInterface $pokemonRepository,
		ItemRepositoryInterface $itemRepository,
		MovesetRatedItemRepositoryInterface $movesetRatedItemRepository
	) {
		$this->formatRepository = $formatRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->itemRepository = $itemRepository;
		$this->movesetRatedItemRepository = $movesetRatedItemRepository;
	}


	/**
	 * Set the item usage history of the requested Pokémon in the requested
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

		$this->movesetRatedItems = $this->movesetRatedItemRepository->getByFormatAndRatingAndPokemon(
			$format->id(),
			$rating,
			$pokemon->id()
		);
	}

	/**
	 * Set the item usage history of the requested Pokémon in the requested
	 * format for the requested item across all ratings.
	 *
	 * @param string $formatIdentifier
	 * @param string $pokemonIdentifier
	 * @param string $itemIdentifier
	 *
	 * @return void
	 */
	public function setItemUsage(
		string $formatIdentifier,
		string $pokemonIdentifier,
		string $itemIdentifier
	) : void {
		$format = $this->formatRepository->getByIdentifier($formatIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$item = $this->itemRepository->getByIdentifier($itemIdentifier);

		$this->movesetRatedItems = $this->movesetRatedItemRepository->getByFormatAndPokemonAndItem(
			$format->id(),
			$pokemon->id(),
			$item->id()
		);
	}

	/**
	 * Get the item usage history.
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getUsage() : array
	{
		return $this->movesetRatedItems;
	}
}
