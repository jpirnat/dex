<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityFlagRepositoryInterface;
use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;

final class DexAbilityFlagModel
{
	private array $flag = [];
	private array $abilities = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly AbilityFlagRepositoryInterface $flagRepository,
		private readonly DexAbilityRepositoryInterface $dexAbilityRepository,
	) {}


	/**
	 * Set data for the dex ability flag page.
	 */
	public function setData(
		string $vgIdentifier,
		string $abilityFlagIdentifier,
		LanguageId $languageId,
	) : void {
		$this->flag = [];
		$this->abilities = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$flag = $this->flagRepository->getByIdentifier($abilityFlagIdentifier);
		$flagId = $flag->getId();

		$this->versionGroupModel->setWithAbilityFlag($flagId);

		$dexFlag = $this->flagRepository->getByIdPlural(
			$versionGroupId,
			$flagId,
			$languageId,
		);
		$this->flag = [
			'identifier' => $dexFlag->getIdentifier(),
			'name' => $dexFlag->getName(),
			'description' => $dexFlag->getDescription(),
		];

		$this->abilities = $this->dexAbilityRepository->getByVgAndFlag(
			$versionGroupId,
			$flagId,
			$languageId,
		);
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getFlag() : array
	{
		return $this->flag;
	}

	public function getAbilities() : array
	{
		return $this->abilities;
	}
}
