<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\PokemonMoves;

use Jp\Dex\Domain\Items\ItemDescriptionRepositoryInterface;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;

final readonly class PokemonMoveFormatter
{
	public function __construct(
		private TmRepositoryInterface $tmRepository,
		private ItemDescriptionRepositoryInterface $itemDescriptionRepository,
		private MoveNameRepositoryInterface $moveNameRepository,
	) {}

	/**
	 * Format in words the method through which this Pokémon move is learned.
	 * Examples: "Level 15", "TM92", "Egg".
	 */
	public function format(
		PokemonMove $pokemonMove,
		LanguageId $languageId,
	) : string {
		$method = $pokemonMove->moveMethodId->value();

		if ($method === MoveMethodId::LEVEL_UP) {
			return 'Level ' . $pokemonMove->level;
		}

		if ($method === MoveMethodId::MACHINE) {
			$tm = $this->tmRepository->getByVersionGroupAndMove(
				$pokemonMove->versionGroupId,
				$pokemonMove->moveId,
			);

			$itemName = $this->itemDescriptionRepository->getByItem(
				$pokemonMove->versionGroupId,
				$languageId,
				$tm->itemId,
			);

			return $itemName->name;
		}

		if ($method === MoveMethodId::EGG) {
			return 'Egg';
		}

		if ($method === MoveMethodId::SKETCH) {
			$moveName = $this->moveNameRepository->getByLanguageAndMove(
				$languageId,
				new MoveId(MoveId::SKETCH),
			);

			return $moveName->name;
		}

		if ($method === MoveMethodId::TUTOR) {
			return 'Tutor';
		}

		if ($method === MoveMethodId::LIGHT_BALL) {
			$itemName = $this->itemDescriptionRepository->getByItem(
				$pokemonMove->versionGroupId,
				$languageId,
				new ItemId(ItemId::LIGHT_BALL),
			);

			return $itemName->name;
		}

		if ($method === MoveMethodId::FORM_CHANGE) {
			return 'Form Change';
		}

		if ($method === MoveMethodId::EVOLUTION) {
			return 'Evolution';
		}

		if ($method === MoveMethodId::REMINDER) {
			return 'Move Reminder';
		}

		if ($method === MoveMethodId::SHADOW) {
			return 'Shadow';
		}

		if ($method === MoveMethodId::PURIFICATION) {
			return 'Purification';
		}

		return ''; // This should never happen.
	}
}
