<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
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
		private GenerationModel $generationModel,
		private TypeRepositoryInterface $typeRepository,
		private DexTypeRepositoryInterface $dexTypeRepository,
		private TypeMatchupRepositoryInterface $typeMatchupRepository,
		private StatNameModel $statNameModel,
		private DexPokemonRepositoryInterface $dexPokemonRepository,
		private DexMoveRepositoryInterface $dexMoveRepository,
	) {}


	/**
	 * Set data for the dex type page.
	 */
	public function setData(
		string $generationIdentifier,
		string $typeIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$type = $this->typeRepository->getByIdentifier($typeIdentifier);

		$this->generationModel->setGensSince($type->getIntroducedInGenerationId());

		$dexType = $this->dexTypeRepository->getById(
			$type->getId(),
			$languageId
		);

		$this->type = [
			'identifier' => $dexType->getIdentifier(),
			'name' => $dexType->getName(),
		];

		// Get the type matchups.
		$this->types = $this->dexTypeRepository->getMainByGeneration(
			$generationId,
			$languageId
		);
		$this->damageDealt = [];
		$this->damageTaken = [];
		$attackingMatchups = $this->typeMatchupRepository->getByAttackingType(
			$generationId,
			$type->getId()
		);
		$defendingMatchups = $this->typeMatchupRepository->getByDefendingType(
			$generationId,
			$type->getId()
		);
		foreach ($attackingMatchups as $matchup) {
			$defendingTypeId = $matchup->getDefendingTypeId()->value();
			$defendingType = $this->types[$defendingTypeId];
			$identifier = $defendingType->getIdentifier();
			$this->damageDealt[$identifier] = $matchup->getMultiplier();
		}
		foreach ($defendingMatchups as $matchup) {
			$attackingTypeId = $matchup->getAttackingTypeId()->value();
			$attackingType = $this->types[$attackingTypeId];
			$identifier = $attackingType->getIdentifier();
			$this->damageTaken[$identifier] = $matchup->getMultiplier();
		}

		// Get stat name abbreviations.
		$this->stats = $this->statNameModel->getByGeneration($generationId, $languageId);

		// Get Pokémon with this type.
		$this->pokemon = $this->dexPokemonRepository->getByType(
			$generationId,
			$type->getId(),
			$languageId
		);

		// Get moves with this type.
		$this->moves = $this->dexMoveRepository->getByType(
			$generationId,
			$type->getId(),
			$languageId
		);
	}


	/**
	 * Get the generation model.
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
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
