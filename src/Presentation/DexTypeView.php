<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypeModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class DexTypeView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexTypeModel $dexTypeModel */
	private $dexTypeModel;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param DexTypeModel $dexTypeModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		DexTypeModel $dexTypeModel,
		DexFormatter $dexFormatter
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->dexTypeModel = $dexTypeModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex ability page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexTypeModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$type = $this->dexTypeModel->getType();
		$pokemon = $this->dexTypeModel->getPokemon();
		$moves = $this->dexTypeModel->getMoves();

		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);
		$moves = $this->dexFormatter->formatDexMoves($moves);
		// TODO: Sort Pokémon somehow. Add a sort value to class Pokemon?

		// Navigational breadcrumbs.
		$generationIdentifier = $generation->getIdentifier();
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'url' => "/dex/$generationIdentifier/types",
				'text' => 'Types',
			],
			[
				'text' => $type['name'],
			]
		];

		$content = $this->twig->render(
			'html/dex/type.twig',
			$this->baseView->getBaseVariables() + [
				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],
				'type' => [
					'identifier' => $type['identifier'],
				],
				'title' => 'Types - ' . $type['name'],
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'stats' => ['HP', 'Atk', 'Def', 'SpA', 'SpD', 'Spe', 'BST'],
				// TODO: Pull these stat names from somewhere else.
				'pokemons' => $pokemon,
				'moves' => $moves,
			]
		);

		return new HtmlResponse($content);
	}
}
