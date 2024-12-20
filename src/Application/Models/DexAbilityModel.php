<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Abilities\AbilityDescriptionRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\Flags\AbilityFlagRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;

final class DexAbilityModel
{
	private(set) array $ability = [];
	private(set) array $flags = [];
	private(set) array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly AbilityRepositoryInterface $abilityRepository,
		private readonly AbilityNameRepositoryInterface $abilityNameRepository,
		private readonly AbilityDescriptionRepositoryInterface $abilityDescriptionRepository,
		private readonly AbilityFlagRepositoryInterface $flagRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
	) {}


	/**
	 * Set data for the dex ability page.
	 */
	public function setData(
		string $vgIdentifier,
		string $abilityIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$ability = $this->abilityRepository->getByIdentifier($abilityIdentifier);
		$abilityId = $ability->getId();

		$this->versionGroupModel->setWithAbility($abilityId);

		$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
			$languageId,
			$abilityId,
		);

		$abilityDescription = $this->abilityDescriptionRepository->getByAbility(
			$versionGroupId,
			$languageId,
			$abilityId,
		);

		$this->ability = [
			'identifier' => $ability->getIdentifier(),
			'name' => $abilityName->getName(),
			'description' => $abilityDescription->getDescription(),
		];

		$this->setFlags($versionGroupId, $abilityId, $languageId);

		$this->stats = $this->dexStatRepository->getByVersionGroup($versionGroupId, $languageId);

		$this->pokemon = $this->dexPokemonRepository->getWithAbility(
			$versionGroupId,
			$ability->getId(),
			$languageId,
		);
	}

	private function setFlags(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : void {
		$this->flags = [];

		$allFlags = $this->flagRepository->getByVersionGroupSingular(
			$versionGroupId,
			$languageId,
		);
		$abilityFlagIds = $this->flagRepository->getByAbility(
			$versionGroupId,
			$abilityId,
		);

		foreach ($allFlags as $flagId => $flag) {
			$has = isset($abilityFlagIds[$flagId]); // Does the ability have this flag?

			$this->flags[] = [
				'identifier' => $flag->getIdentifier(),
				'name' => $flag->getName(),
				'description' => $flag->getDescription(),
				'has' => $has,
			];
		}
	}
}
