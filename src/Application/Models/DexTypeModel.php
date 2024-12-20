<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeMatchupRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class DexTypeModel
{
	private(set) array $type = [];

	/** @var DexType[] $dexType */
	private(set) array $types = [];

	/** @var float[] $damageDealt */
	private(set) array $damageDealt = [];

	/** @var float[] $damageTaken */
	private(set) array $damageTaken = [];

	private(set) array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private(set) array $pokemon = [];

	/** @var DexMove[] $moves */
	private(set) array $moves = [];


	public function __construct(
		private(set) readonly VersionGroupModel $versionGroupModel,
		private readonly TypeRepositoryInterface $typeRepository,
		private readonly DexTypeRepositoryInterface $dexTypeRepository,
		private readonly TypeMatchupRepositoryInterface $typeMatchupRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly DexMoveRepositoryInterface $dexMoveRepository,
	) {}


	/**
	 * Set data for the dex type page.
	 */
	public function setData(
		string $vgIdentifier,
		string $typeIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$type = $this->typeRepository->getByIdentifier($typeIdentifier);

		$this->versionGroupModel->setWithType($type->getId());

		$dexType = $this->dexTypeRepository->getById(
			$type->getId(),
			$languageId,
		);

		$this->type = [
			'identifier' => $dexType->getIdentifier(),
			'name' => $dexType->getName(),
		];

		// Get the type matchups.
		$this->types = $this->dexTypeRepository->getMainByVersionGroup(
			$versionGroupId,
			$languageId,
		);
		$this->damageDealt = [];
		$this->damageTaken = [];
		$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
			$this->versionGroupModel->versionGroup->getGenerationId(),
			$type->getId(),
		);
		$defendingMatchups = $this->typeMatchupRepository->getByDefendingType(
			$this->versionGroupModel->versionGroup->getGenerationId(),
			$type->getId(),
		);
		foreach ($attackingMatchups as $matchup) {
			$defendingTypeIdentifier = $matchup->getDefendingTypeIdentifier();
			$this->damageDealt[$defendingTypeIdentifier] = $matchup->getMultiplier();
		}
		foreach ($defendingMatchups as $matchup) {
			$attackingTypeIdentifier = $matchup->getAttackingTypeIdentifier();
			$this->damageTaken[$attackingTypeIdentifier] = $matchup->getMultiplier();
		}

		$this->stats = $this->dexStatRepository->getByVersionGroup($versionGroupId, $languageId);

		// Get Pokémon with this type.
		$this->pokemon = $this->dexPokemonRepository->getByType(
			$versionGroupId,
			$type->getId(),
			$languageId,
		);

		// Get moves with this type.
		$this->moves = $this->dexMoveRepository->getByType(
			$versionGroupId,
			$type->getId(),
			$languageId,
		);
	}
}
