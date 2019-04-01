<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;

class DexTypeFactory
{
	/** @var PokemonTypeRepositoryInterface $pokemonTypeRepository */
	private $pokemonTypeRepository;

	/** @var TypeRepositoryInterface $typeRepository */
	private $typeRepository;

	/** @var TypeIconRepositoryInterface $typeIconRepository */
	private $typeIconRepository;

	/**
	 * Constructor.
	 *
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 * @param TypeRepositoryInterface $typeRepository
	 * @param TypeIconRepositoryInterface $typeIconRepository
	 */
	public function __construct(
		PokemonTypeRepositoryInterface $pokemonTypeRepository,
		TypeRepositoryInterface $typeRepository,
		TypeIconRepositoryInterface $typeIconRepository
	) {
		$this->pokemonTypeRepository = $pokemonTypeRepository;
		$this->typeRepository = $typeRepository;
		$this->typeIconRepository = $typeIconRepository;
	}

	/**
	 * Get the dex type for this type.
	 *
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexType
	 */
	public function getDexType(
		TypeId $typeId,
		LanguageId $languageId
	) : DexType {
		$type = $this->typeRepository->getById($typeId);

		$typeIcon = $this->typeIconRepository->getByLanguageAndType(
			$languageId,
			$typeId
		);

		return new DexType(
			$type->getIdentifier(),
			$typeIcon->getIcon()
		);
	}

	/**
	 * Get the dex types for this Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[]
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);

		$types = [];

		foreach ($pokemonTypes as $pokemonType) {
			$types[] = $this->getDexType($pokemonType->getTypeId(), $languageId);
		}

		return $types;
	}

	/**
	 * Get all dex types for this generation and language.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getAll(GenerationId $generationId, LanguageId $languageId) : array
	{
		$types = $this->typeRepository->getByGeneration($generationId);
		$typeIcons = $this->typeIconRepository->getByLanguage($languageId);

		$dexTypes = [];

		foreach ($types as $type) {
			$typeId = $type->getId()->value();

			$dexTypes[$typeId] = new DexType(
				$types[$typeId]->getIdentifier(),
				$typeIcons[$typeId]->getIcon()
			);
		}

		return $dexTypes;
	}
}
