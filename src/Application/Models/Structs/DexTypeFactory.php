<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\TypeIcons\TypeIconRepositoryInterface;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
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
			$type = $this->typeRepository->getById($pokemonType->getTypeId());
			$typeIcon = $this->typeIconRepository->getByGenerationAndLanguageAndType(
				$generationId,
				$languageId,
				$type->getId()
			);

			$types[] = new DexType(
				$type->getIdentifier(),
				$typeIcon->getImage()
			);
		}

		return $types;
	}
}
