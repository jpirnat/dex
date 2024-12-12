<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\AdvancedPokemonSearch;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\Abilities\DexAbilityRepositoryInterface;
use Jp\Dex\Domain\EggGroups\DexEggGroupRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

final class AdvancedPokemonSearchIndexModel
{
	private array $types = [];
	private array $abilities = [];
	private array $eggGroups = [];
	private array $genderRatios = [];
	private array $moves = [];
	private array $stats = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly DexTypeRepositoryInterface $dexTypeRepository,
		private readonly DexAbilityRepositoryInterface $dexAbilityRepository,
		private readonly DexEggGroupRepositoryInterface $dexEggGroupRepository,
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
		$this->types = [];
		$this->abilities = [];
		$this->eggGroups = [];
		$this->genderRatios = [];
		$this->moves = [];
		$this->stats = [];

		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$this->versionGroupModel->setSinceGeneration(new GenerationId(1));

		$types = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		foreach ($types as $type) {
			$this->types[] = [
				'identifier' => $type->getIdentifier(),
				'name' => $type->getName(),
			];
		}

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

		$eggGroups = $this->dexEggGroupRepository->getAll($languageId);
		foreach ($eggGroups as $eggGroup) {
			$this->eggGroups[] = [
				'identifier' => $eggGroup->getIdentifier(),
				'name' => $eggGroup->getName(),
			];
		}

		$genderRatios = GenderRatio::getAll();
		foreach ($genderRatios as $genderRatio) {
			$this->genderRatios[] = [
				'value' => $genderRatio->value(),
				'description' => $genderRatio->getDescription(),
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

	public function getTypes() : array
	{
		return $this->types;
	}

	public function getAbilities() : array
	{
		return $this->abilities;
	}

	public function getEggGroups() : array
	{
		return $this->eggGroups;
	}

	public function getGenderRatios() : array
	{
		return $this->genderRatios;
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
