<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\DexNatureRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexNaturesModel
{
	private array $natures = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly DexNatureRepositoryInterface $dexNatureRepository,
	) {}


	/**
	 * Set data for the dex natures page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(3));


		$this->natures = $this->dexNatureRepository->getByLanguage($languageId);
	}


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the natures.
	 */
	public function getNatures() : array
	{
		return $this->natures;
	}
}
