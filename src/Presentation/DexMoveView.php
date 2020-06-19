<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexMove\DexMoveModel;
use Jp\Dex\Application\Models\DexMove\DexMovePokemon;
use Jp\Dex\Application\Models\DexMove\DexMovePokemonMethod;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexMoveView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private DexMoveModel $dexMoveModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexMoveModel $dexMoveModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexMoveModel $dexMoveModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->dexMoveModel = $dexMoveModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex move page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexMoveModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$move = $this->dexMoveModel->getMove();

		$versionGroups = $this->dexMoveModel->getVersionGroups();
		$showAbilities = $generation->getId()->value() >= 3;

		$dexMovePokemonModel = $this->dexMoveModel->getDexMovePokemonModel();
		$stats = $dexMovePokemonModel->getStats();
		$methods = $dexMovePokemonModel->getMethods();

		// Sort Pokémon within each move method.
		$bySort = function (DexMovePokemon $a, DexMovePokemon $b) : int {
			return $a->getSort() <=> $b->getSort();
		};
		foreach ($methods as $methodId => $method) {
			uasort($method->getPokemon(), $bySort);
		}

		// How many columns does the move Pokémon table have?
		$colspan = 4 + count($versionGroups) + count($stats)
			+ ($showAbilities ? 1 : 0);

		// Navigational breadcrumbs.
		$generationIdentifier = $generation->getIdentifier();
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'url' => "/dex/$generationIdentifier/moves",
			'text' => 'Moves',
		], [
			'text' => $move['name'],
		]];

		$content = $this->renderer->render(
			'html/dex/move.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Moves - ' . $move['name'],
				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'move' => [
					'identifier' => $move['identifier'],
				],
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),
				'showAbilities' => $showAbilities,
				'stats' => $stats,
				'methods' => $this->formatDexMovePokemonMethods($methods),
				'colspan' => $colspan,
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Transform an array of dex move Pokémon method objects into a renderable
	 * data array.
	 *
	 * @param DexMovePokemonMethod[] $dexMovePokemonMethods
	 *
	 * @return array
	 */
	private function formatDexMovePokemonMethods(array $dexMovePokemonMethods) : array
	{
		$methods = [];

		foreach ($dexMovePokemonMethods as $method) {
			$methods[] = [
				'identifier' => $method->getIdentifier(),
				'name' => $method->getName(),
				'description' => $method->getDescription(),
				'pokemon' => $this->formatDexMovePokemon($method->getPokemon()),
			];
		}

		return $methods;
	}

	/**
	 * Transform an array of dex move Pokémon objects into a renderable data
	 * array.
	 *
	 * @param DexMovePokemon[] $dexMovePokemon
	 *
	 * @return array
	 */
	private function formatDexMovePokemon(array $dexMovePokemon) : array
	{
		$pokemons = [];

		foreach ($dexMovePokemon as $pokemon) {
			$pokemons[] = [
				'versionGroupData' => $pokemon->getVersionGroupData(),
				'icon' => $pokemon->getIcon(),
				'identifier' => $pokemon->getIdentifier(),
				'name' => $pokemon->getName(),
				'types' => $this->dexFormatter->formatDexTypes($pokemon->getTypes()),
				'abilities' => $this->dexFormatter->formatDexPokemonAbilities(
					$pokemon->getAbilities()
				),
				'baseStats' => $pokemon->getBaseStats(),
				'bst' => $pokemon->getBaseStatTotal(),
				'sort' => $pokemon->getSort(),
			];
		}

		return $pokemons;
	}
}
