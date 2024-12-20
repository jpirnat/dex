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
	private(set) array $items = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexItemRepositoryInterface $dexItemRepository,
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
}
