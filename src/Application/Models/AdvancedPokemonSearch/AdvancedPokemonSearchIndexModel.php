<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedPokemonSearch;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class AdvancedPokemonSearchIndexModel
{
	private array $abilities = [];
	private array $moves = [];
	private array $stats = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly DexAbilityRepositoryInterface $dexAbilityRepository,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
	) {}


	/**
	 * Set data for the advanced Pokémon search page.
	 */
	public function setData(
		string $vgIdentifier,
		LanguageId $languageId,
	) : void {
		$this->abilities = [];
		$this->moves = [];
		$this->stats = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$abilities = $this->dexAbilityRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		foreach ($abilities as $ability) {
			$this->abilities[] = [
				'identifier' => $ability['identifier'],
				'name' => $ability['name'],
			];
		}

		$moves = $this->dexMoveRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		foreach ($moves as $move) {
			$this->moves[] = [
				'identifier' => $move->getIdentifier(),
				'name' => $move->getName(),
			];
		}

		$this->stats = $this->dexStatRepository->getByVersionGroup($versionGroupId, $languageId);
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getAbilities() : array
	{
		return $this->abilities;
	}

	public function getMoves() : array
	{
		return $this->moves;
	}

	public function getStats() : array
	{
		return $this->stats;
	}
}
