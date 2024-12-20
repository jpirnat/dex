<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\EggGroups\DexEggGroupRepositoryInterface;
use Jp\Dex\Domain\EggGroups\EggGroupRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;

final class DexEggGroupModel
{
	private(set) array $eggGroup = [];
	private(set) array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private(set) array $pokemon = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly EggGroupRepositoryInterface $eggGroupRepository,
		private readonly DexEggGroupRepositoryInterface $dexEggGroupRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
	) {}


	/**
	 * Set data for the dex egg group page.
	 */
	public function setData(
		string $vgIdentifier,
		string $eggGroupIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setWithBreeding();

		$eggGroup = $this->eggGroupRepository->getByIdentifier($eggGroupIdentifier);
		$eggGroupId = $eggGroup->getId();

		$dexEggGroup = $this->dexEggGroupRepository->getById(
			$eggGroupId,
			$languageId,
		);

		$this->eggGroup = [
			'identifier' => $dexEggGroup->getIdentifier(),
			'name' => $dexEggGroup->getName(),
		];

		$this->stats = $this->dexStatRepository->getByVersionGroup($versionGroupId, $languageId);

		$this->pokemon = $this->dexPokemonRepository->getInEggGroup(
			$versionGroupId,
			$eggGroup->getId(),
			$languageId,
		);
	}
}
