<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexPokemon\DexPokemonModel;
use Jp\Dex\Application\Models\DexPokemon\DexPokemonMove;
use Jp\Dex\Application\Models\DexPokemon\DexPokemonMoveMethod;
use Jp\Dex\Domain\Items\MachineType;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\Versions\DexVersionGroup;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;

final readonly class DexPokemonView
{
	public function __construct(
		private DexPokemonModel $dexPokemonModel,
		private DexFormatter $dexFormatter,
		private EvolutionTableFormatter $evolutionTableFormatter,
	) {}

	/**
	 * Get data for the dex Pokémon page.
	 */
	public function getData() : ResponseInterface
	{
		$versionGroupModel = $this->dexPokemonModel->versionGroupModel;
		$versionGroup = $versionGroupModel->versionGroup;
		$versionGroups = $versionGroupModel->versionGroups;

		$pokemon = $this->dexPokemonModel->pokemon;
		$pokemon = $this->dexFormatter->formatExpandedDexPokemon($pokemon);
		if (!$versionGroup->hasAbilities()) {
			$pokemon['abilities'] = [];
		}

		$stats = $this->dexPokemonModel->stats;
		$breedingPartnersSearchUrl = $this->dexPokemonModel->breedingPartnersSearchUrl;

		$dexPokemonMatchupsModel = $this->dexPokemonModel->dexPokemonMatchupsModel;
		$types = $dexPokemonMatchupsModel->types;
		$damageTaken = $dexPokemonMatchupsModel->damageTaken;
		$damageTakenAbilities = $dexPokemonMatchupsModel->abilities;

		$dexPokemonEvolutionsModel = $this->dexPokemonModel->dexPokemonEvolutionsModel;
		$evolutionTableRows = $dexPokemonEvolutionsModel->evolutionTableRows;

		$dexPokemonMovesModel = $this->dexPokemonModel->dexPokemonMovesModel;
		$categories = $dexPokemonMovesModel->categories;
		$learnsetVgs = $dexPokemonMovesModel->learnsetVgs;
		$methods = $dexPokemonMovesModel->methods;

		// Sort moves within each move method.
		$byName = function (DexPokemonMove $a, DexPokemonMove $b) : int {
			return $a->getName() <=> $b->getName();
		};

		$vgIdentifiers = array_map(function (DexVersionGroup $vg) : string {
			return $vg->getIdentifier();
		}, $learnsetVgs);
		$byMachine = function (DexPokemonMove $a, DexPokemonMove $b) use ($vgIdentifiers) : int {
			$aVgData = $a->getVersionGroupData();
			$bVgData = $b->getVersionGroupData();

			// Get the most recent version groups in which each move was a TM.
			// Since version groups are already sorted, we just need to iterate
			// backwards through $vgIdentifiers until we hit one whose identifier
			// has move vgData.
			$keys = array_keys($vgIdentifiers);
			$count = count($keys);
			$aHowFarBack = 1;
			$bHowFarBack = 1;
			for (; ; $aHowFarBack++) {
				$vgIdentifier = $vgIdentifiers[$keys[$count - $aHowFarBack]];
				if (isset($aVgData[$vgIdentifier])) {
					break;
				}
			}
			for (; ; $bHowFarBack++) {
				$vgIdentifier = $vgIdentifiers[$keys[$count - $bHowFarBack]];
				if (isset($bVgData[$vgIdentifier])) {
					break;
				}
			}

			// Get the version group identifiers we settled on.
			$aVgIdentifier = $vgIdentifiers[$keys[$count - $aHowFarBack]];
			$bVgIdentifier = $vgIdentifiers[$keys[$count - $bHowFarBack]];

			// Get a sortable number for both TMs.
			// TMs should always come before TRs, and TRs should always come before HMs.
			$aMachine = $aVgData[$aVgIdentifier] ?? [];
			$bMachine = $bVgData[$bVgIdentifier] ?? [];
			$aNumber = $aMachine['number'] ?? 999;
			$bNumber = $bMachine['number'] ?? 999;
			if (($aMachine['machineType'] ?? '') === MachineType::TR) {
				$aNumber += 200;
			}
			if (($bMachine['machineType'] ?? '') === MachineType::TR) {
				$bNumber += 200;
			}
			if (($aMachine['machineType'] ?? '') === MachineType::HM) {
				$aNumber += 400;
			}
			if (($bMachine['machineType'] ?? '') === MachineType::HM) {
				$bNumber += 400;
			}

			// If these moves have different TM numbers, the one with the lower TM number comes first.
			if ($comparison = $aNumber <=> $bNumber) {
				return $comparison;
			}

			// If these moves have the same TM number, the one from the later game comes first.
			return $aHowFarBack <=> $bHowFarBack;
		};

		foreach ($methods as $moveMethodId => $method) {
			switch ($moveMethodId) {
				// TODO: sorting algorithms for the other methods.
				case MoveMethodId::MACHINE:
					$method->sortMoves($byMachine);
					break;
				case MoveMethodId::EGG:
				case MoveMethodId::TUTOR:
					$method->sortMoves($byName);
					break;
			}
		}

		// Navigational breadcrumbs.
		$vgIdentifier = $versionGroup->getIdentifier();
		$breadcrumbs = [[
			'url' => "/dex/$vgIdentifier",
			'text' => 'Dex',
		], [
			'url' => "/dex/$vgIdentifier/pokemon",
			'text' => 'Pokémon',
		], [
			'text' => $pokemon['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Pokémon - ' . $pokemon['name'],

				'versionGroup' => [
					'id' => $versionGroup->getId()->value(),
					'identifier' => $versionGroup->getIdentifier(),
					'generationId' => $versionGroup->getGenerationId()->value(),
					'hasEvYields' => $versionGroup->hasEvYields(),
					'hasBreeding' => $versionGroup->hasBreeding(),
					'hasMoveDescriptions' => $versionGroup->getId()->hasMoveDescriptions(),
				],

				'breadcrumbs' => $breadcrumbs,
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),

				'pokemon' => $pokemon,
				'stats' => $stats,

				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageTaken' => $damageTaken,
				'damageTakenAbilities' => $damageTakenAbilities,

				'breedingPartnersSearchUrl' => $breedingPartnersSearchUrl,

				'evolutionTableRows' => $this->evolutionTableFormatter->formatRows($evolutionTableRows),

				'categories' => $this->dexFormatter->formatDexCategories($categories),
				'methods' => $this->formatDexPokemonMoveMethods($methods),
				'learnsetVgs' => $this->dexFormatter->formatDexVersionGroups($learnsetVgs),
			]
		]);
	}

	/**
	 * Transform an array of dex Pokémon move method objects into a renderable
	 * data array.
	 *
	 * @param DexPokemonMoveMethod[] $dexPokemonMoveMethods
	 */
	public function formatDexPokemonMoveMethods(array $dexPokemonMoveMethods) : array
	{
		$methods = [];

		foreach ($dexPokemonMoveMethods as $method) {
			$methods[] = [
				'identifier' => $method->identifier,
				'name' => $method->name,
				'description' => $method->description,
				'moves' => $this->formatDexPokemonMoves($method->moves),
			];
		}

		return $methods;
	}

	/**
	 * Transform an array of dex Pokémon move objects into a renderable data
	 * array.
	 *
	 * @param DexPokemonMove[] $dexPokemonMoves
	 */
	public function formatDexPokemonMoves(array $dexPokemonMoves) : array
	{
		$moves = [];

		foreach ($dexPokemonMoves as $move) {
			$moves[] = [
				'vgData' => $move->getVersionGroupData(),
				'identifier' => $move->getIdentifier(),
				'name' => $move->getName(),
				'type' => $this->dexFormatter->formatDexType($move->getType()),
				'category' => $this->dexFormatter->formatDexCategory($move->getCategory()),
				'pp' => $move->getPP(),
				'power' => $move->getPower(),
				'accuracy' => $move->getAccuracy(),
				'description' => $move->getDescription(),
			];
		}

		return $moves;
	}
}
