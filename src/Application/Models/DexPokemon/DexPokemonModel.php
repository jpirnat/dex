<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\DexPokemon;

use Jp\Dex\Application\Models\VersionGroupModel;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\EggGroups\EggGroupIdentifier;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemon;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\VgPokemonNotFoundException;
use Jp\Dex\Domain\Stats\DexStatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;

final class DexPokemonModel
{
	private ?ExpandedDexPokemon $pokemon = null;
	private array $stats = [];
	private string $breedingPartnersSearchUrl = '';


	public function __construct(
		private readonly VersionGroupModel $versionGroupModel,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly ExpandedDexPokemonRepositoryInterface $expandedDexPokemonRepository,
		private readonly DexStatRepositoryInterface $dexStatRepository,
		private readonly DexPokemonMatchupsModel $dexPokemonMatchupsModel,
		private readonly DexPokemonEvolutionsModel $dexPokemonEvolutionsModel,
		private readonly DexPokemonMovesModel $dexPokemonMovesModel,
	) {}


	/**
	 * Set data for the dex Pokémon page.
	 */
	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		LanguageId $languageId,
	) : void {
		$this->pokemon = null;
		$this->stats = [];
		$this->breedingPartnersSearchUrl = '';

		try {
			$versionGroupId = $this->versionGroupModel->setByIdentifier($vgIdentifier);
			$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		} catch (VersionGroupNotFoundException | PokemonNotFoundException) {
			return;
		}

		$this->versionGroupModel->setWithPokemon($pokemon->getId());

		try {
			$this->pokemon = $this->expandedDexPokemonRepository->getById(
				$versionGroupId,
				$pokemon->getId(),
				$languageId,
			);
		} catch (VgPokemonNotFoundException) {
			return;
		}

		$this->stats = $this->dexStatRepository->getByVersionGroup(
			$versionGroupId,
			$languageId,
		);

		// Set the Pokémon's matchups.
		$this->dexPokemonMatchupsModel->setData(
			$this->versionGroupModel->getVersionGroup(),
			$pokemon->getId(),
			$languageId,
			$this->pokemon->getAbilities(),
		);

		$this->setBreedingPartnersSearchUrl($vgIdentifier);

		// Set the Pokémon's evolutions.
		$this->dexPokemonEvolutionsModel->setData(
			$versionGroupId,
			$pokemon->getId(),
			$languageId,
		);

		$this->dexPokemonMovesModel->setData(
			$versionGroupId,
			$pokemon->getId(),
			$languageId,
		);
	}

	public function setBreedingPartnersSearchUrl(
		string $vgIdentifier,
	) : void {
		$versionGroup = $this->versionGroupModel->getVersionGroup();
		$eggGroups = $this->pokemon->getEggGroups();
		$genderRatio = $this->pokemon->getGenderRatio()->value();

		if (!$versionGroup->hasBreeding()
			|| $eggGroups === []
			|| $eggGroups[0]->getIdentifier() === EggGroupIdentifier::UNDISCOVERED
			|| $eggGroups[0]->getIdentifier() === EggGroupIdentifier::DITTO
			|| $genderRatio === GenderRatio::GENDER_UNKNOWN
		) {
			return;
		}

		$eggGroups = array_map(
			function (DexEggGroup $e) : string {
				return $e->getIdentifier();
			},
			$this->pokemon->getEggGroups(),
		);
		$eggGroups = implode('.', $eggGroups);

		$genderRatios = GenderRatio::GENDER_UNKNOWN;
		if ($genderRatio === GenderRatio::MALE_ONLY) {
			$genderRatios .= ".$genderRatio";
		}
		if ($genderRatio === GenderRatio::FEMALE_ONLY) {
			$genderRatios .= ".$genderRatio";
		}

		$this->breedingPartnersSearchUrl = "/dex/$vgIdentifier/advanced-pokemon-search?eggGroups=$eggGroups&genderRatios=$genderRatios&genderRatiosOperator=none";
	}


	public function getVersionGroupModel() : VersionGroupModel
	{
		return $this->versionGroupModel;
	}

	public function getPokemon() : ?ExpandedDexPokemon
	{
		return $this->pokemon;
	}

	public function getStats() : array
	{
		return $this->stats;
	}

	public function getBreedingPartnersSearchUrl() : string
	{
		return $this->breedingPartnersSearchUrl;
	}

	public function getDexPokemonMatchupsModel() : DexPokemonMatchupsModel
	{
		return $this->dexPokemonMatchupsModel;
	}

	public function getDexPokemonEvolutionsModel() : DexPokemonEvolutionsModel
	{
		return $this->dexPokemonEvolutionsModel;
	}

	public function getDexPokemonMovesModel() : DexPokemonMovesModel
	{
		return $this->dexPokemonMovesModel;
	}
}
