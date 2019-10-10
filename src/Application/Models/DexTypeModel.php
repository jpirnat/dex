<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Moves\DexMoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Types\TypeNameRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

final class DexTypeModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TypeNameRepositoryInterface $typeNameRepository */
	private $typeNameRepository;

	/** @var DexPokemonRepositoryInterface $dexPokemonRepository */
	private $dexPokemonRepository;

	/** @var DexMoveRepositoryInterface $dexMoveRepository */
	private $dexMoveRepository;


	/** @var array $type */
	private $type = [];

	/** @var DexPokemon[] $pokemon */
	private $pokemon = [];

	/** @var DexMove[] $moves */
	private $moves = [];


	/**
	 * Constructor.
	 *
	 * @param GenerationModel $generationModel
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TypeNameRepositoryInterface $typeNameRepository
	 * @param DexPokemonRepositoryInterface $dexPokemonRepository
	 * @param DexMoveRepositoryInterface $dexMoveRepository
	 */
	public function __construct(
		GenerationModel $generationModel,
		TypeRepositoryInterface $typeRepository,
		TypeNameRepositoryInterface $typeNameRepository,
		DexPokemonRepositoryInterface $dexPokemonRepository,
		DexMoveRepositoryInterface $dexMoveRepository
	) {
		$this->generationModel = $generationModel;
		$this->typeRepository = $typeRepository;
		$this->typeNameRepository = $typeNameRepository;
		$this->dexPokemonRepository = $dexPokemonRepository;
		$this->dexMoveRepository = $dexMoveRepository;
	}

	/**
	 * Set data for the dex type page.
	 *
	 * @param string $generationIdentifier
	 * @param string $typeIdentifier
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		string $generationIdentifier,
		string $typeIdentifier,
		LanguageId $languageId
	) : void {
		$generationId = $this->generationModel->setByIdentifier($generationIdentifier);

		$type = $this->typeRepository->getByIdentifier($typeIdentifier);

		$this->generationModel->setGensSince($type->getIntroducedInGenerationId());

		$typeName = $this->typeNameRepository->getByLanguageAndType(
			$languageId,
			$type->getId()
		);

		$this->type = [
			'identifier' => $type->getIdentifier(),
			'name' => $typeName->getName(),
		];

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
	 *
	 * @return GenerationModel
	 */
	public function getGenerationModel() : GenerationModel
	{
		return $this->generationModel;
	}

	/**
	 * Get the type.
	 *
	 * @return array
	 */
	public function getType() : array
	{
		return $this->type;
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
