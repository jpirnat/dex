<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypeModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexTypeView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexTypeModel $dexTypeModel */
	private $dexTypeModel;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexTypeModel $dexTypeModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexTypeModel $dexTypeModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
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
		$showAbilities = $generation->getId()->value() >= 3;
		$pokemon = $this->dexTypeModel->getPokemon();
		$pokemon = $this->dexFormatter->formatDexPokemon($pokemon);

		$showMoveDescriptions = $generation->getId()->value() >= 3;
		$moves = $this->dexTypeModel->getMoves();
		$moves = $this->dexFormatter->formatDexMoves($moves);


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

		$content = $this->renderer->render(
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
				'showAbilities' => $showAbilities,
				'stats' => ['HP', 'Atk', 'Def', 'SpA', 'SpD', 'Spe'],
				// TODO: Pull these stat names from somewhere else.
				'pokemons' => $pokemon,
				'showMoveDescriptions' => $showMoveDescriptions,
				'moves' => $moves,
			]
		);

		return new HtmlResponse($content);
	}
}
