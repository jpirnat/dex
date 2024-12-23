<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;

final class DexAbilityFlagModel
{
	private(set) array $flag = [];
	private(set) array $abilities = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
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

		$this->versionGroupModel->setWithAbilityFlag($flag->id);

		$dexFlag = $this->flagRepository->getByIdPlural(
			$versionGroupId,
			$flag->id,
			$languageId,
		);
		$this->flag = [
			'identifier' => $dexFlag->identifier,
			'name' => $dexFlag->name,
			'description' => $dexFlag->description,
		];

		$this->abilities = $this->dexAbilityRepository->getByVgAndFlag(
			$versionGroupId,
			$flag->id,
			$languageId,
		);
	}
}
