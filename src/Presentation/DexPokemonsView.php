<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexPokemonsModel;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

final class DexPokemonsView
{
	/** @var RendererInterface $renderer */
	private $renderer;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexPokemonsModel $dexPokemonsModel */
	private $dexPokemonsModel;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexPokemonsModel $dexPokemonsModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexPokemonsModel $dexPokemonsModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->dexPokemonsModel = $dexPokemonsModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex Pokémons page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexPokemonsModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$showAbilities = $generation->getId()->value() >= 3;
		$statAbbreviations = $this->dexPokemonsModel->getStatAbbreviations();
		$pokemon = $this->dexPokemonsModel->getPokemon();

		// Navigational breadcrumbs.
		$breadcrumbs = [[
			'text' => 'Dex',
		], [
			'text' => 'Pokémon',
		]];

		$content = $this->renderer->render(
			'html/dex/pokemons.twig',
			$this->baseView->getBaseVariables() + [
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'title' => 'Pokémon',
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'showAbilities' => $showAbilities,
				'statAbbreviations' => $statAbbreviations,
				'pokemons' => $this->dexFormatter->formatDexPokemon($pokemon),
			]
		);

		return new HtmlResponse($content);
	}
}
