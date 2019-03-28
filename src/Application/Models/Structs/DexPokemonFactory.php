<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Types\PokemonTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;

class DexPokemonFactory
{
	/** @var DexTypeFactory $dexTypeFactory */
	private $dexTypeFactory;

	/** @var FormIconRepositoryInterface $formIconRepository */
	private $formIconRepository;

	/** @var PokemonRepositoryInterface $pokemonRepository */
	private $pokemonRepository;

	/** @var PokemonNameRepositoryInterface $pokemonNameRepository */
	private $pokemonNameRepository;

	/** @var DexPokemonAbilityFactory $dexPokemonAbilityFactory */
	private $dexPokemonAbilityFactory;

	/** @var BaseStatRepositoryInterface $baseStatRepository */
	private $baseStatRepository;

	/** @var PokemonTypeRepositoryInterface $pokemonTypeRepository */
	private $pokemonTypeRepository;

	/**
	 * Constructor.
	 *
	 * @param DexTypeFactory $dexTypeFactory
	 * @param FormIconRepositoryInterface $formIconRepository
	 * @param PokemonRepositoryInterface $pokemonRepository
	 * @param PokemonNameRepositoryInterface $pokemonNameRepository
	 * @param DexPokemonAbilityFactory $dexPokemonAbilityFactory
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 */
	public function __construct(
		DexTypeFactory $dexTypeFactory,
		FormIconRepositoryInterface $formIconRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		DexPokemonAbilityFactory $dexPokemonAbilityFactory,
		BaseStatRepositoryInterface $baseStatRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository
	) {
		$this->dexTypeFactory = $dexTypeFactory;
		$this->formIconRepository = $formIconRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->dexPokemonAbilityFactory = $dexPokemonAbilityFactory;
		$this->baseStatRepository = $baseStatRepository;
		$this->pokemonTypeRepository = $pokemonTypeRepository;
	}

	/**
	 * Get the dex Pokémon for this Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon
	 */
	public function getDexPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : DexPokemon {
		// Get the Pokémon's form icon.
		$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
			$generationId,
			new FormId($pokemonId->value()),
			false,
			false
		);

		// Get the Pokémon.
		$pokemon = $this->pokemonRepository->getById($pokemonId);

		// Get the Pokémon's name.
		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId
		);

		// Get the Pokémon's types.
		$types = $this->dexTypeFactory->getByPokemon(
			$generationId,
			$pokemonId,
			$languageId
		);

		// Get the Pokémon's abilities.
		$abilities = $this->dexPokemonAbilityFactory->getByPokemon(
			$generationId,
			$pokemonId,
			$languageId
		);

		// Get the Pokémon's base stats.
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);
		$stats = [];
		$statIds = StatId::getByGeneration($generationId);
		foreach ($statIds as $statId) {
			$stats[] = $baseStats->get($statId)->getValue();
		}
		$stats[] = array_sum($stats); // End with base stat total.

		return new DexPokemon(
			$formIcon->getImage(),
			$pokemon->getIdentifier(),
			$pokemonName->getName(),
			$types,
			$abilities,
			$stats
		);
	}

	/**
	 * Get dex Pokémon by generation and type.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon[]
	 */
	public function getByGenerationAndType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array {
		$pokemonTypes = $this->pokemonTypeRepository->getByGenerationAndType(
			$generationId,
			$typeId
		);

		$pokemon = [];

		foreach ($pokemonTypes as $pokemonType) {
			$pokemon[] = $this->getDexPokemon(
				$generationId,
				$pokemonType->getPokemonId(),
				$languageId
			);
		}

		return $pokemon;
	}
}
