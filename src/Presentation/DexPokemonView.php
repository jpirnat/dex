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

final class DexPokemonView
{
	private DexPokemonModel $dexPokemonModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param DexPokemonModel $dexPokemonModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		DexPokemonModel $dexPokemonModel,
		DexFormatter $dexFormatter
	) {
		$this->dexPokemonModel = $dexPokemonModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex Pokémon page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexPokemonModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$pokemon = $this->dexPokemonModel->getPokemon();
		$abilities = $this->dexPokemonModel->getAbilities();

		$dexPokemonMatchupsModel = $this->dexPokemonModel->getDexPokemonMatchupsModel();
		$types = $dexPokemonMatchupsModel->getTypes();
		$damageTaken = $dexPokemonMatchupsModel->getDamageTaken();
		$damageTakenAbilities = $dexPokemonMatchupsModel->getAbilities();

		$versionGroups = $this->dexPokemonModel->getVersionGroups();
		$showMoveDescriptions = $generation->getId()->value() >= 3;

		$dexPokemonMovesModel = $this->dexPokemonModel->getDexPokemonMovesModel();
		$methods = $dexPokemonMovesModel->getMethods();

		// Sort moves within each move method.
		$byName = function (DexPokemonMove $a, DexPokemonMove $b) : int {
			return $a->getName() <=> $b->getName();
		};

		$vgIdentifiers = array_map(function (DexVersionGroup $vg) : string {
			return $vg->getIdentifier();
		}, $versionGroups);
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
					uasort($method->getMoves(), $byMachine);
					break;
				case MoveMethodId::EGG:
				case MoveMethodId::TUTOR:
					uasort($method->getMoves(), $byName);
					break;
			}
		}

		// Navigational breadcrumbs.
		$generationIdentifier = $generation->getIdentifier();
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'url' => "/dex/$generationIdentifier/pokemon",
			'text' => 'Pokémon',
		], [
			'text' => $pokemon['name'],
		]];

		return new JsonResponse([
			'data' => [
				'title' => 'Porydex - Pokémon - ' . $pokemon['name'],

				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],

				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),

				'pokemon' => $pokemon,
				'abilities' => $abilities,

				'types' => $this->dexFormatter->formatDexTypes($types),
				'damageTaken' => $damageTaken,
				'damageTakenAbilities' => $damageTakenAbilities,

				'methods' => $this->formatDexPokemonMoveMethods($methods),
				'versionGroups' => $this->dexFormatter->formatDexVersionGroups($versionGroups),
				'showMoveDescriptions' => $showMoveDescriptions,
			]
		]);
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
	 * Transform an array of dex Pokémon move objects into a renderable data
	 * array.
	 *
	 * @param DexPokemonMove[] $dexPokemonMoves
	 *
	 * @return array
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
