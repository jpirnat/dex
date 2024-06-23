<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\BreedingChains;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\BreedingChains\BreedingChainFinder;
use Jp\Dex\Domain\EggGroups\EggGroupNameRepositoryInterface;
use Jp\Dex\Domain\EggGroups\PokemonEggGroupRepositoryInterface;
use Jp\Dex\Domain\FormIcons\FormIconRepositoryInterface;
use Jp\Dex\Domain\Forms\FormId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveNameRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNameRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\PokemonMoves\PokemonMove;
use Jp\Dex\Domain\PokemonMoves\PokemonMoveFormatter;
use Jp\Dex\Domain\Species\SpeciesRepositoryInterface;
use Jp\Dex\Domain\Versions\DexVersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class BreedingChainsModel
{
	private array $pokemon = [];
	private array $move = [];

	/** @var BreedingChainRecord[][] $chains */
	private array $chains = [];


	public function __construct(
		private VersionGroupModel $versionGroupModel,
		private PokemonRepositoryInterface $pokemonRepository,
		private MoveRepositoryInterface $moveRepository,
		private BreedingChainFinder $breedingChainFinder,
		private FormIconRepositoryInterface $formIconRepository,
		private DexVersionGroupRepositoryInterface $dexVersionGroupRepository,
		private PokemonNameRepositoryInterface $pokemonNameRepository,
		private MoveNameRepositoryInterface $moveNameRepository,
		private VersionGroupRepositoryInterface $versionGroupRepository,
		private PokemonEggGroupRepositoryInterface $pokemonEggGroupRepository,
		private EggGroupNameRepositoryInterface $eggGroupNameRepository,
		private SpeciesRepositoryInterface $speciesRepository,
		private PokemonMoveFormatter $pokemonMoveFormatter,
	) {}


	/**
	 * Set breeding chain data for this Pokémon, move, and version group combination.
	 */
	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		string $moveIdentifier,
		LanguageId $languageId,
	) : void {
		$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);

		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$move = $this->moveRepository->getByIdentifier($moveIdentifier);

		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemon->getId()
		);
		$this->pokemon = [
			'identifier' => $pokemon->getIdentifier(),
			'name' => $pokemonName->getName(),
		];

		$moveName = $this->moveNameRepository->getByLanguageAndMove($languageId, $move->getId());
		$this->move = [
			'name' => $moveName->getName(),
		];

		/** @var PokemonMove[][] $chains */
		$chains = $this->breedingChainFinder->findChains(
			$pokemon->getId(),
			$move->getId(),
			$versionGroupId,
		);

		$this->chains = [];
		foreach ($chains as $chain) {
			$chainId = [];
			$records = [];
			foreach ($chain as $pokemonMove) {
				$chainId[] = $pokemonMove->getPokemonId()->value();
				$records[] = $this->getRecord($pokemonMove, $languageId);
			}
			$chainId = implode('-', $chainId);
			$this->chains[$chainId] = $records;
		}
	}

	/**
	 * Create the breeding chain record for this Pokémon move.
	 */
	private function getRecord(
		PokemonMove $pokemonMove,
		LanguageId $languageId,
	) : BreedingChainRecord {
		$pokemonId = $pokemonMove->getPokemonId();

		$formIcon = $this->formIconRepository->getByVgAndFormAndFemaleAndRight(
			$pokemonMove->getVersionGroupId(),
			new FormId($pokemonId->value()),
			false,
			false,
		);

		$versionGroup = $this->dexVersionGroupRepository->getById(
			$pokemonMove->getVersionGroupId(),
			$languageId,
		);

		$pokemon = $this->pokemonRepository->getById($pokemonId);

		$pokemonName = $this->pokemonNameRepository->getByLanguageAndPokemon(
			$languageId,
			$pokemonId,
		);


		$eggGroupNames = [];
		$pokemonEggGroups = $this->pokemonEggGroupRepository->getByPokemon(
			$versionGroup->getGenerationId(),
			$pokemonId,
		);
		foreach ($pokemonEggGroups as $pokemonEggGroup) {
			$eggGroupName = $this->eggGroupNameRepository->getByLanguageAndEggGroup(
				$languageId,
				$pokemonEggGroup->getEggGroupId()
			);
			$eggGroupNames[] = $eggGroupName->getName();
		}

		$species = $this->speciesRepository->getById($pokemon->getSpeciesId());

		$genderRatio = $pokemon->getGenderRatio();

		return new BreedingChainRecord(
			$formIcon->getImage(),
			$pokemon->getIdentifier(),
			$pokemonName->getName(),
			$versionGroup,
			$eggGroupNames,
			$species->getBaseEggCycles(),
			"$genderRatio.png",
			$this->genderRatioText($genderRatio),
			$this->pokemonMoveFormatter->format($pokemonMove, $languageId),
		);
	}

	/**
	 * Get the alt text for this gender ratio.
	 */
	public function genderRatioText(int $genderRatio) : string
	{
		if ($genderRatio === -1) {
			return 'Genderless';
		}
		if ($genderRatio === 0) {
			return '100% male';
		}
		if ($genderRatio === 1) {
			return '87.5% male, 12.5% female';
		}
		if ($genderRatio === 2) {
			return '75% male, 25% female';
		}
		if ($genderRatio === 4) {
			return '50% male, 50% female';
		}
		if ($genderRatio === 6) {
			return '25% male, 75% female';
		}
		if ($genderRatio === 7) {
			return '12.5% male, 87.5% female';
		}
		if ($genderRatio === 8) {
			return '100% female';
		}
		return '';
	}


	/**
	 * Get the generation model.
	 */
	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	/**
	 * Get the Pokémon.
	 */
	public function getPokemon() : array
	{
		return $this->pokemon;
	}

	/**
	 * Get the move.
	 */
	public function getMove() : array
	{
		return $this->move;
	}

	/**
	 * Get the chains.
	 *
	 * @return BreedingChainRecord[][]
	 */
	public function getChains() : array
	{
		return $this->chains;
	}
}

