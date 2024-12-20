<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\DexNatureRepositoryInterface;

final class DexNaturesModel
{
	private(set) array $natures = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexNatureRepositoryInterface $dexNatureRepository,
	) {}


	/**
	 * Set data for the dex natures page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->natures = [];

		$this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithNatures();

		$this->natures = $this->dexNatureRepository->getByLanguage($languageId);
	}
}
