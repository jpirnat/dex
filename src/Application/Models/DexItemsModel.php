<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Items\DexItem;
use Jp\Dex\Domain\Items\DexItemRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexItemsModel
{
	/** @var DexItem[] $items */
	private array $items = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private DexItemRepositoryInterface $dexItemRepository,
	) {}


	/**
	 * Set data for the dex items page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$this->items = $this->dexItemRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
	}


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the items.
	 *
	 * @return DexItem[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}
}
