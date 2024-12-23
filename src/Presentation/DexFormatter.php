<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorPokemon;
use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\Abilities\ExpandedDexPokemonAbility;
use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\Items\DexItem;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemon;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Versions\DexVersion;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Jp\Dex\Domain\Versions\VersionGroup;

final readonly class DexFormatter
{
	/**
	 * Transform an array of generation objects into a renderable data array.
	 * This will most commonly be used for the generation control.
	 *
	 * @param VersionGroup[] $versionGroups
	 */
	public function formatVersionGroups(array $versionGroups) : array
	{
		$g = [];

		foreach ($versionGroups as $versionGroup) {
			$g[] = [
				'identifier' => $versionGroup->getIdentifier(),
				'name' => $versionGroup->getAbbreviation(),
			];
		}

		return $g;
	}

	/**
	 * Transform an array of version group objects into a renderable data array.
	 *
	 * @param DexVersionGroup[] $versionGroups
	 */
	public function formatDexVersionGroups(array $versionGroups) : array
	{
		$vg = [];

		foreach ($versionGroups as $versionGroup) {
			$vg[] = $this->formatDexVersionGroup($versionGroup);
		}

		return $vg;
	}

	/**
	 * Transform an array of version group objects into a renderable data array.
	 */
	public function formatDexVersionGroup(DexVersionGroup $versionGroup) : array
	{
		return [
			'identifier' => $versionGroup->getIdentifier(),
			'generationId' => $versionGroup->getGenerationId()->value(),
			'name' => $versionGroup->getName(),
			'versions' => $this->formatDexVersions($versionGroup->getVersions()),
		];
	}

	/**
	 * Transform an array of version group objects into a renderable data array.
	 *
	 * @param DexVersion[] $versions
	 */
	private function formatDexVersions(array $versions) : array
	{
		$v = [];

		foreach ($versions as $version) {
			$v[] = $this->formatDexVersion($version);
		}

		return $v;
	}

	/**
	 * Transform an array of version group objects into a renderable data array.
	 */
	private function formatDexVersion(DexVersion $version) : array
	{
		return [
			'abbreviation' => $version->getAbbreviation(),
		];
	}

	/**
	 * Transform an array of dex Pokémon objects into a renderable data array.
	 *
	 * @param DexPokemon[] $dexPokemons
	 */
	public function formatDexPokemon(array $dexPokemons) : array
	{
		$pokemon = [];

		foreach ($dexPokemons as $dexPokemon) {
			$pokemon[] = [
				'icon' => $dexPokemon->getIcon(),
				'identifier' => $dexPokemon->getIdentifier(),
				'name' => $dexPokemon->getName(),
				'types' => $this->formatDexTypes($dexPokemon->getTypes()),
				'abilities' => $this->formatDexPokemonAbilities($dexPokemon->getAbilities()),
				'baseStats' => $dexPokemon->getBaseStats(),
				'bst' => $dexPokemon->getBst(),
				'eggGroups' => $this->formatDexEggGroups($dexPokemon->getEggGroups()),
				'genderRatio' => [
					'icon' => $dexPokemon->getGenderRatio()->getIcon(),
					'description' => $dexPokemon->getGenderRatio()->getDescription(),
				],
				'eggCycles' => $dexPokemon->getEggCycles(),
				'stepsToHatch' => $dexPokemon->getStepsToHatch(),
				'evYield' => $dexPokemon->getEvYield(),
				'sort' => $dexPokemon->getSort(),
			];
		}

		return $pokemon;
	}

	public function formatExpandedDexPokemon(ExpandedDexPokemon $pokemon) : array
	{
		return [
			'identifier' => $pokemon->getIdentifier(),
			'name' => $pokemon->getName(),
			'sprite' => $pokemon->getSprite(),
			'types' => $this->formatDexTypes($pokemon->getTypes()),
			'abilities' => $this->formatExpandedDexPokemonAbilities($pokemon->getAbilities()),
			'baseStats' => $pokemon->getBaseStats(),
			'bst' => $pokemon->getBst(),
			'baseExperience' => $pokemon->getBaseExperience(),
			'evYield' => $pokemon->getEvYield(),
			'evTotal' => $pokemon->getEvTotal(),
			'catchRate' => $pokemon->getCatchRate(),
			'baseFriendship' => $pokemon->getBaseFriendship(),
			'experienceGroup' => [
				'name' => $pokemon->getExperienceGroup()->getName(),
				'points' => $pokemon->getExperienceGroup()->getPoints(),
			],
			'eggGroups' => $this->formatDexEggGroups($pokemon->getEggGroups()),
			'genderRatio' => [
				'icon' => $pokemon->getGenderRatio()->getIcon(),
				'description' => $pokemon->getGenderRatio()->getDescription(),
			],
			'eggCycles' => $pokemon->getEggCycles(),
			'stepsToHatch' => $pokemon->getStepsToHatch(),
		];
	}

	/**
	 * Transform an array of dex type objects into a renderable data array.
	 *
	 * @param DexType[] $dexTypes
	 */
	public function formatDexTypes(array $dexTypes) : array
	{
		$types = [];

		foreach ($dexTypes as $dexType) {
			$types[] = $this->formatDexType($dexType);
		}

		return $types;
	}

	/**
	 * Transform a dex type object into a renderable data array.
	 */
	public function formatDexType(DexType $dexType) : array
	{
		return [
			'identifier' => $dexType->getIdentifier(),
			'name' => $dexType->getName(),
			'icon' => $dexType->getIcon(),
		];
	}

	/**
	 * Transform a dex category object into a renderable data array.
	 */
	public function formatDexCategories(array $dexCategories) : array
	{
		$categories = [];

		foreach ($dexCategories as $dexCategory) {
			$categories[] = $this->formatDexCategory($dexCategory);
		}

		return $categories;
	}

	/**
	 * Transform a dex category object into a renderable data array.
	 */
	public function formatDexCategory(DexCategory $dexCategory) : array
	{
		return [
			'identifier' => $dexCategory->getIdentifier(),
			'icon' => $dexCategory->getIcon(),
			'name' => $dexCategory->getName(),
		];
	}

	/**
	 * Transform an array of dex Pokémon ability objects into a renderable data array.
	 *
	 * @param DexPokemonAbility[] $dexPokemonAbilities
	 */
	public function formatDexPokemonAbilities(array $dexPokemonAbilities) : array
	{
		$abilities = [];

		foreach ($dexPokemonAbilities as $dexPokemonAbility) {
			$abilities[] = [
				'identifier' => $dexPokemonAbility->identifier,
				'name' => $dexPokemonAbility->name,
				'isHiddenAbility' => $dexPokemonAbility->isHiddenAbility,
			];
		}

		return $abilities;
	}

	/**
	 * Transform an array of dex Pokémon ability objects into a renderable data array.
	 *
	 * @param ExpandedDexPokemonAbility[] $abilities
	 */
	public function formatExpandedDexPokemonAbilities(array $abilities) : array
	{
		$a = [];

		foreach ($abilities as $ability) {
			$a[] = [
				'identifier' => $ability->identifier,
				'name' => $ability->name,
				'description' => $ability->description,
				'isHiddenAbility' => $ability->isHiddenAbility,
			];
		}

		return $a;
	}

	/**
	 * Transform an array of dex egg group objects into a renderable data array.
	 *
	 * @param DexEggGroup[] $dexEggGroups
	 */
	public function formatDexEggGroups(array $dexEggGroups) : array
	{
		$eggGroups = [];

		foreach ($dexEggGroups as $dexEggGroup) {
			$eggGroups[] = [
				'identifier' => $dexEggGroup->getIdentifier(),
				'name' => $dexEggGroup->getName(),
			];
		}

		return $eggGroups;
	}

	/**
	 * Transform an array of dex move objects into a renderable data array.
	 *
	 * @param DexMove[] $dexMoves
	 */
	public function formatDexMoves(array $dexMoves) : array
	{
		$moves = [];

		foreach ($dexMoves as $dexMove) {
			$moves[] = $this->formatDexMove($dexMove);
		}

		return $moves;
	}

	/**
	 * Transform a dex move object into a renderable data array.
	 */
	public function formatDexMove(DexMove $dexMove) : array
	{
		return [
			'identifier' => $dexMove->getIdentifier(),
			'name' => $dexMove->getName(),
			'type' => $this->formatDexType($dexMove->getType()),
			'category' => $this->formatDexCategory($dexMove->getCategory()),
			'pp' => $dexMove->getPP(),
			'power' => $dexMove->getPower(),
			'accuracy' => $dexMove->getAccuracy(),
			'description' => str_replace("\n", ' ', $dexMove->getDescription()),
		];
	}

	/**
	 * Transform an array of dex item objects into a renderable data array.
	 *
	 * @param DexItem[] $dexItems
	 */
	public function formatDexItems(array $dexItems) : array
	{
		$items = [];

		foreach ($dexItems as $dexItem) {
			$items[] = $this->formatDexItem($dexItem);
		}

		return $items;
	}

	/**
	 * Transform a dex move object into a renderable data array.
	 */
	public function formatDexItem(DexItem $dexItem) : array
	{
		return [
			'icon' => $dexItem->getIcon(),
			'identifier' => $dexItem->getIdentifier(),
			'name' => $dexItem->getName(),
			'description' => $dexItem->getDescription(),
		];
	}

	/**
	 * Transform an array of IV calculator Pokémon objects into a renderable data array.
	 *
	 * @param IvCalculatorPokemon[] $ivCalculatorPokemons
	 */
	public function formatIvCalculatorPokemons(array $ivCalculatorPokemons) : array
	{
		$pokemon = [];

		foreach ($ivCalculatorPokemons as $ivCalculatorPokemon) {
			$pokemon[] = $this->formatIvCalculatorPokemon($ivCalculatorPokemon);
		}

		return $pokemon;
	}

	/**
	 * Transform an IV calculator Pokémon object into a renderable data array.
	 */
	private function formatIvCalculatorPokemon(IvCalculatorPokemon $ivCalculatorPokemon) : array
	{
		return [
			'identifier' => $ivCalculatorPokemon->getIdentifier(),
			'name' => $ivCalculatorPokemon->getName(),
			'sprite' => $ivCalculatorPokemon->getSprite(),
			'types' => $this->formatDexTypes($ivCalculatorPokemon->getTypes()),
			'baseStats' => $ivCalculatorPokemon->getBaseStats(),
			'bst' => $ivCalculatorPokemon->getBst(),
		];
	}
}
