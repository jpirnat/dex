<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\Structs\DexMove;
use Jp\Dex\Application\Models\Structs\DexPokemon;
use Jp\Dex\Application\Models\Structs\DexPokemonAbility;
use Jp\Dex\Application\Models\Structs\DexPokemonMove;
use Jp\Dex\Application\Models\Structs\DexPokemonMoveMethod;
use Jp\Dex\Application\Models\Structs\DexType;
use Jp\Dex\Domain\Versions\Generation;
use Jp\Dex\Domain\Versions\VersionGroup;

class DexFormatter
{
	/**
	 * Transform an array of generation objects into a renderable data array.
	 * This will most commonly be used for the generation control.
	 *
	 * @param Generation[] $generations
	 *
	 * @return array
	 */
	public function formatGenerations(array $generations) : array
	{
		$g = [];

		foreach ($generations as $generation) {
			$g[] = [
				'identifier' => $generation->getIdentifier(),
				'name' => mb_strtoupper($generation->getIdentifier()),
			];
		}

		return $g;
	}

	/**
	 * Transform an array of version group objects into a renderable data array.
	 *
	 * @param VersionGroup[] $versionGroups
	 *
	 * @return array
	 */
	public function formatVersionGroups(array $versionGroups) : array
	{
		$vg = [];

		foreach ($versionGroups as $versionGroup) {
			$vg[] = [
				'id' => $versionGroup->getId()->value(),
				'identifier' => $versionGroup->getIdentifier(),
				'icon' => $versionGroup->getIcon(),
			];
		}

		return $vg;
	}

	/**
	 * Transform an array of dex Pokémon objects into a renderable data array.
	 *
	 * @param DexPokemon[] $dexPokemons
	 *
	 * @return array
	 */
	public function formatDexPokemon(array $dexPokemons) : array
	{
		$pokemon = [];

		foreach ($dexPokemons as $dexPokemon) {
			$pokemon[] = [
				'formIcon' => $dexPokemon->getFormIcon(),
				'identifier' => $dexPokemon->getPokemonIdentifier(),
				'name' => $dexPokemon->getPokemonName(),
				'types' => $this->formatDexTypes($dexPokemon->getTypes()),
				'abilities' => $this->formatDexPokemonAbilities($dexPokemon->getAbilities()),
				'baseStats' => $dexPokemon->getBaseStats(),
			];
		}

		return $pokemon;
	}

	/**
	 * Transform an array of dex type objects into a renderable data array.
	 *
	 * @param DexType[] $dexTypes
	 *
	 * @return array
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
	 *
	 * @param DexType $dexType
	 *
	 * @return array
	 */
	public function formatDexType(DexType $dexType) : array
	{
		return [
			'identifier' => $dexType->getTypeIdentifier(),
			'icon' => $dexType->getTypeIcon(),
		];
	}

	/**
	 * Transform an array of dex Pokémon ability objects into a renderable data array.
	 *
	 * @param DexPokemonAbility[] $dexPokemonAbilities
	 *
	 * @return array
	 */
	public function formatDexPokemonAbilities(array $dexPokemonAbilities) : array
	{
		$abilities = [];

		foreach ($dexPokemonAbilities as $dexPokemonAbility) {
			$abilities[] = [
				'identifier' => $dexPokemonAbility->getAbilityIdentifier(),
				'name' => $dexPokemonAbility->getAbilityName(),
				'isHiddenAbility' => $dexPokemonAbility->isHiddenAbility(),
			];
		}

		return $abilities;
	}

	/**
	 * Transform an array of dex move objects into a renderable data array.
	 *
	 * @param DexMove[] $dexMoves
	 *
	 * @return array
	 */
	public function formatDexMoves(array $dexMoves) : array
	{
		$moves = [];

		foreach ($dexMoves as $dexMove) {
			$moves[] = [
				'identifier' => $dexMove->getMoveIdentifier(),
				'name' => $dexMove->getMoveName(),
				'type' => $this->formatDexType($dexMove->getType()),
				'categoryIcon' => $dexMove->getCategoryIcon(),
				'pp' => $dexMove->getPP(),
				'power' => $dexMove->getPower(),
				'accuracy' => $dexMove->getAccuracy(),
				'priority' => $dexMove->getPriority(),
				'description' => $dexMove->getMoveDescription(),
			];
		}

		return $moves;
	}

	/**
	 * Transform an array of dex Pokémon move method objects into a renderable
	 * data array.
	 *
	 * @param DexPokemonMoveMethod[] $dexPokemonMoveMethods
	 *
	 * @return array
	 */
	public function formatDexPokemonMoveMethods(array $dexPokemonMoveMethods) : array
	{
		$methods = [];

		foreach ($dexPokemonMoveMethods as $method) {
			$methods[] = [
				'identifier' => $method->getIdentifier(),
				'name' => $method->getName(),
				'description' => $method->getDescription(),
				'moves' => $this->formatDexPokemonMoves($method->getMoves()),
			];
		}

		return $methods;
	}

	/**
	 * Transform an array of dex Pokémon move objects into a renderable data array.
	 *
	 * @param DexPokemonMove[] $dexPokemonMoves
	 *
	 * @return array
	 */
	public function formatDexPokemonMoves(array $dexPokemonMoves) : array
	{
		$moves = [];

		foreach ($dexPokemonMoves as $dexPokemonMove) {
			$power = $dexPokemonMove->getPower();
			if ($power === 0) {
				$power = '&mdash;';
			}
			if ($power === 1) {
				$power = '*';
			}

			$accuracy = $dexPokemonMove->getAccuracy();
			if ($accuracy === 101) {
				$accuracy = '&mdash;';
			}

			$moves[] = [
				'versionGroupIds' => $dexPokemonMove->getVersionGroupIds(),
				'identifier' => $dexPokemonMove->getMoveIdentifier(),
				'name' => $dexPokemonMove->getMoveName(),
				'type' => $this->formatDexType($dexPokemonMove->getType()),
				'categoryIcon' => $dexPokemonMove->getCategoryIcon(),
				'pp' => $dexPokemonMove->getPP(),
				'power' => $power,
				'accuracy' => $accuracy,
				'description' => $dexPokemonMove->getMoveDescription(),
			];
		}

		return $moves;
	}

	
}
