<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;

final class DexTmsModel
{
	private(set) array $machines = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly TmRepositoryInterface $tmRepository,
		private readonly DexItemRepositoryInterface $dexItemRepository,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
	) {}


	/**
	 * Set data for the dex moves page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->machines = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithTms();

		$machines = $this->tmRepository->getByVersionGroup($versionGroupId);
		$items = $this->dexItemRepository->getTmsByVg(
			$versionGroupId,
			$languageId,
		);
		$moves = $this->dexMoveRepository->getTmsByVg(
			$versionGroupId,
			$languageId,
		);

		foreach ($machines as $machine) {
			$itemId = $machine->itemId->value;
			$moveId = $machine->moveId->value;

			if (!isset($items[$itemId])) {
				// This is probably TM95 in BW, which is unavailable.
				continue;
			}

			$item = $items[$itemId];
			$move = $moves[$moveId];

			$this->machines[] = [
				'item' => $item,
				'move' => $move,
			];
		}
	}
}
