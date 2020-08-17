<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexPokemon\DexPokemonModel;
use Jp\Dex\Application\Models\DexPokemon\DexPokemonMove;
use Jp\Dex\Application\Models\DexPokemon\DexPokemonMoveMethod;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
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

		$versionGroups = $this->dexPokemonModel->getVersionGroups();
		$showMoveDescriptions = $generation->getId()->value() >= 3;

		$dexPokemonMovesModel = $this->dexPokemonModel->getDexPokemonMovesModel();
		$methods = $dexPokemonMovesModel->getMethods();

		// Sort moves within each move method.
		$byName = function (DexPokemonMove $a, DexPokemonMove $b) : int {
			return $a->getName() <=> $b->getName();
		};
		foreach ($methods as $moveMethodId => $method) {
			switch ($moveMethodId) {
				// TODO: sorting algorithms for the other methods.
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
