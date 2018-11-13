<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\Structs;

use Jp\Dex\Domain\Abilities\AbilityNameRepositoryInterface;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Abilities\PokemonAbilityRepositoryInterface;
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

	/** @var PokemonAbilityRepositoryInterface $pokemonAbilityRepository */
	private $pokemonAbilityRepository;

	/** @var AbilityRepositoryInterface $abilityRepository */
	private $abilityRepository;

	/** @var AbilityNameRepositoryInterface $abilityNameRepository */
	private $abilityNameRepository;

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
	 * @param PokemonAbilityRepositoryInterface $pokemonAbilityRepository
	 * @param AbilityRepositoryInterface $abilityRepository
	 * @param AbilityNameRepositoryInterface $abilityNameRepository
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 * @param PokemonTypeRepositoryInterface $pokemonTypeRepository
	 */
	public function __construct(
		DexTypeFactory $dexTypeFactory,
		FormIconRepositoryInterface $formIconRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonAbilityRepositoryInterface $pokemonAbilityRepository,
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		BaseStatRepositoryInterface $baseStatRepository,
		PokemonTypeRepositoryInterface $pokemonTypeRepository
	) {
		$this->dexTypeFactory = $dexTypeFactory;
		$this->formIconRepository = $formIconRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->pokemonAbilityRepository = $pokemonAbilityRepository;
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
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
		$pokemonAbilities = $this->pokemonAbilityRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);
		$abilities = [];
		foreach ($pokemonAbilities as $pokemonAbility) {
			$ability = $this->abilityRepository->getById($pokemonAbility->getAbilityId());
			$abilityName = $this->abilityNameRepository->getByLanguageAndAbility(
				$languageId,
				$ability->getId()
			);

			$abilities[] = new DexPokemonAbility(
				$ability->getIdentifier(),
				$abilityName->getName(),
				$pokemonAbility->isHiddenAbility()
			);
		}

		// Get the Pokémon's base stats.
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);
		$stats = [];
		// Rely on stats always being in this order.
		$stats[] = $baseStats->get(new StatId(StatId::HP))->getValue();
		$stats[] = $baseStats->get(new StatId(StatId::ATTACK))->getValue();
		$stats[] = $baseStats->get(new StatId(StatId::DEFENSE))->getValue();
		if ($generationId->value() === 1) {
			$stats[] = $baseStats->get(new StatId(StatId::SPECIAL))->getValue();
		} else {
			$stats[] = $baseStats->get(new StatId(StatId::SPECIAL_ATTACK))->getValue();
			$stats[] = $baseStats->get(new StatId(StatId::SPECIAL_DEFENSE))->getValue();
		}
		$stats[] = $baseStats->get(new StatId(StatId::SPEED))->getValue();
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
