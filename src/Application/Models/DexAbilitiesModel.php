<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Versions\GenerationId;

final class DexAbilitiesModel
{
	private array $abilities = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private DexAbilityRepositoryInterface $dexAbilityRepository,
	) {}


	/**
	 * Set data for the dex abilities page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(3));

		$this->abilities = $this->dexAbilityRepository->getByVersionGroup(
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
	 * Get the abilities.
	 */
	public function getAbilities() : array
	{
		return $this->abilities;
	}
}
