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
	private array $type = [];

	/** @var DexType[] $dexType */
	private array $types = [];

	/** @var float[] $damageDealt */
	private array $damageDealt = [];

	/** @var float[] $damageTaken */
	private array $damageTaken = [];

	private array $stats = [];

	/** @var DexPokemon[] $pokemon */
	private array $pokemon = [];

	/** @var DexMove[] $moves */
	private array $moves = [];


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
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
			$this->versionGroupModel->getVersionGroup()->getGenerationId(),
			$type->getId(),
		);
		$defendingMatchups = $this->typeMatchupRepository->getByDefendingType(
			$this->versionGroupModel->getVersionGroup()->getGenerationId(),
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


	/**
	 * Get the version group model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the type.
	 */
	public function getType() : array
	{
		return $this->type;
	}

	/**
	 * Get the types.
	 *
	 * @return DexType[]
	 */
	public function getTypes() : array
	{
		return $this->types;
	}

	/**
	 * Get the damage dealt matchups.
	 *
	 * @return float[]
	 */
	public function getDamageDealt() : array
	{
		return $this->damageDealt;
	}

	/**
	 * Get the damage taken matchups.
	 *
	 * @return float[]
	 */
	public function getDamageTaken() : array
	{
		return $this->damageTaken;
	}

	/**
	 * Get the stats and their names.
	 */
	public function getStats() : array
	{
		return $this->stats;
	}

	/**
	 * Get the Pokémon.
	 *
	 * @return DexPokemon[]
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * Get the moves.
	 *
	 * @return DexMove[]
	 */
	public function getMoves() : array
	{
		return $this->moves;
	}
}
