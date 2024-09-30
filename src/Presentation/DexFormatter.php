<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\Categories\DexCategory;
use Jp\Dex\Domain\Items\DexItem;
use Jp\Dex\Domain\Moves\DexMove;
use Jp\Dex\Domain\Pokemon\DexPokemon;
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
			'backgroundColor' => $version->getBackgroundColor(),
			'textColor' => $version->getTextColor(),
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
				'sort' => $dexPokemon->getSort(),
			];
		}

		return $pokemon;
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
	public function formatDexCategory(DexCategory $dexCategory) : array
	{
		return [
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
				'identifier' => $dexPokemonAbility->getIdentifier(),
				'name' => $dexPokemonAbility->getName(),
				'isHiddenAbility' => $dexPokemonAbility->isHiddenAbility(),
			];
		}

		return $abilities;
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
			'description' => $dexMove->getDescription(),
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
}
