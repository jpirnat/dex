<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;

final class DexAbilitiesModel
{
	private(set) array $abilities = [];
	private(set) array $flags = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly DexAbilityRepositoryInterface $dexAbilityRepository,
		private readonly AbilityFlagRepositoryInterface $flagRepository,
	) {}


	/**
	 * Set data for the dex abilities page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->abilities = [];
		$this->flags = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithAbilities();

		$this->abilities = $this->dexAbilityRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);

		$flags = $this->flagRepository->getByVersionGroupPlural(
			$versionGroupId,
			$languageId,
		);
		foreach ($flags as $flag) {
			$this->flags[] = [
				'identifier' => $flag->identifier,
				'name' => $flag->name,
				'description' => $flag->description,
			];
		}
	}
}
