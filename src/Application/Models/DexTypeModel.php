<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Application\Models\Structs\DexMove;
use Jp\Dex\Application\Models\Structs\DexMoveFactory;
use Jp\Dex\Application\Models\Structs\DexPokemon;
use Jp\Dex\Application\Models\Structs\DexPokemonFactory;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Types\TypeNameRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;

class DexTypeModel
{
	/** @var GenerationModel $generationModel */
	private $generationModel;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TypeNameRepositoryInterface $typeNameRepository */
	private $typeNameRepository;

	/** @var DexPokemonFactory $dexPokemonFactory */
	private $dexPokemonFactory;

	/** @var DexMoveFactory $dexMoveFactory */
	private $dexMoveFactory;


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
	 * @param DexPokemonFactory $dexPokemonFactory
	 * @param DexMoveFactory $dexMoveFactory
	 */
	public function __construct(
		GenerationModel $generationModel,
		TypeRepositoryInterface $typeRepository,
		TypeNameRepositoryInterface $typeNameRepository,
		DexPokemonFactory $dexPokemonFactory,
		DexMoveFactory $dexMoveFactory
	) {
		$this->generationModel = $generationModel;
		$this->typeRepository = $typeRepository;
		$this->typeNameRepository = $typeNameRepository;
		$this->dexPokemonFactory = $dexPokemonFactory;
		$this->dexMoveFactory = $dexMoveFactory;
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
		$this->pokemon = $this->dexPokemonFactory->getByGenerationAndType(
			$generationId,
			$type->getId(),
			$languageId
		);

		// Get moves with this type.
		$this->moves = $this->dexMoveFactory->getByGenerationAndType(
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
