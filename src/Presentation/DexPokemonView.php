<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexPokemon\DexPokemonModel;
use Jp\Dex\Application\Models\DexPokemon\DexPokemonMove;
use Jp\Dex\Application\Models\DexPokemon\DexPokemonMoveMethod;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

class DexPokemonView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexPokemonModel $dexPokemonModel */
	private $dexPokemonModel;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexPokemonModel $dexPokemonModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexPokemonModel $dexPokemonModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
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

		// How many columns does the Pokémon move table have?
		$colspan = 6 + count($versionGroups) + ($showMoveDescriptions ? 1 : 0);

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

		$content = $this->renderer->render(
			'html/dex/pokemon.twig',
			$this->baseView->getBaseVariables() + [
				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'pokemon' => [
					'identifier' => $pokemon['identifier'],
				],
				'versionGroups' => $this->dexFormatter->formatVersionGroups($versionGroups),
				'showMoveDescriptions' => $showMoveDescriptions,
				'methods' => $this->formatDexPokemonMoveMethods($methods),
				'colspan' => $colspan,
			]
		);

		return new HtmlResponse($content);
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
			$power = $move->getPower();
			if ($power === 0) {
				$power = '—'; // em dash
			}
			if ($power === 1) {
				$power = '*';
			}

			$accuracy = $move->getAccuracy();
			if ($accuracy === 101) {
				$accuracy = '—'; // em dash
			}

			$moves[] = [
				'versionGroupData' => $move->getVersionGroupData(),
				'identifier' => $move->getIdentifier(),
				'name' => $move->getName(),
				'type' => $this->dexFormatter->formatDexType($move->getType()),
				'category' => $this->dexFormatter->formatDexCategory($move->getCategory()),
				'pp' => $move->getPP(),
				'power' => $power,
				'accuracy' => $accuracy,
				'description' => $move->getDescription(),
			];
		}

		return $moves;
	}
}
