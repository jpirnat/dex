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
use Jp\Dex\Domain\Versions\Generation;

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
	 */
	public function __construct(
		DexTypeFactory $dexTypeFactory,
		FormIconRepositoryInterface $formIconRepository,
		PokemonRepositoryInterface $pokemonRepository,
		PokemonNameRepositoryInterface $pokemonNameRepository,
		PokemonAbilityRepositoryInterface $pokemonAbilityRepository,
		AbilityRepositoryInterface $abilityRepository,
		AbilityNameRepositoryInterface $abilityNameRepository,
		BaseStatRepositoryInterface $baseStatRepository
	) {
		$this->dexTypeFactory = $dexTypeFactory;
		$this->formIconRepository = $formIconRepository;
		$this->pokemonRepository = $pokemonRepository;
		$this->pokemonNameRepository = $pokemonNameRepository;
		$this->pokemonAbilityRepository = $pokemonAbilityRepository;
		$this->abilityRepository = $abilityRepository;
		$this->abilityNameRepository = $abilityNameRepository;
		$this->baseStatRepository = $baseStatRepository;
	}

	/**
	 * Get the dex Pokémon for this Pokémon.
	 *
	 * @param Generation $generation
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemon
	 */
	public function getDexPokemon(
		Generation $generation,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : DexPokemon {
		// Get the Pokémon's form icon.
		$formIcon = $this->formIconRepository->getByGenerationAndFormAndFemaleAndRight(
			$generation,
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
			$generation,
			$pokemonId,
			$languageId
		);

		// Get the Pokémon's abilities.
		$pokemonAbilities = $this->pokemonAbilityRepository->getByGenerationAndPokemon(
			$generation,
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
			$generation,
			$pokemonId
		);
		$stats = [];
		// Rely on stats always being in this order.
		$stats[] = $baseStats->get(new StatId(StatId::HP))->getValue();
		$stats[] = $baseStats->get(new StatId(StatId::ATTACK))->getValue();
		$stats[] = $baseStats->get(new StatId(StatId::DEFENSE))->getValue();
		if ($generation->getValue() === 1) {
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
}
