<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\EggGroups\DexEggGroupRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;

final class DexEggGroupsModel
{
	/** @var DexEggGroup[] $eggGroups */
	private(set) array $eggGroups = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexEggGroupRepositoryInterface $dexEggGroupRepository,
	) {}


	/**
	 * Set data for the dex egg groups page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->eggGroups = [];

		$this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithBreeding();

		$this->eggGroups = $this->dexEggGroupRepository->getAll($languageId);
	}
}
