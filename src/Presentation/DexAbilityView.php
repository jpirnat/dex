<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilityModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexAbilityView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private DexAbilityModel $dexAbilityModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexAbilityModel $dexAbilityModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexAbilityModel $dexAbilityModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
		$this->baseView = $baseView;
		$this->dexAbilityModel = $dexAbilityModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex ability page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexAbilityModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

		$ability = $this->dexAbilityModel->getAbility();

		$showAbilities = $generation->getId()->value() >= 3;
		$stats = $this->dexAbilityModel->getStats();
		$normalPokemon = $this->dexAbilityModel->getNormalPokemon();
		$hiddenPokemon = $this->dexAbilityModel->getHiddenPokemon();

		$normalPokemon = $this->dexFormatter->formatDexPokemon($normalPokemon);
		$hiddenPokemon = $this->dexFormatter->formatDexPokemon($hiddenPokemon);

		// Navigational breadcrumbs.
		$generationIdentifier = $generation->getIdentifier();
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'url' => "/dex/$generationIdentifier/abilities",
				'text' => 'Abilities',
			],
			[
				'text' => $ability['name'],
			]
		];

		$content = $this->renderer->render(
			'html/dex/ability.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Abilities - ' . $ability['name'],
				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],
				'ability' => $ability,
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'showAbilities' => $showAbilities,
				'stats' => $stats,
				'pokemons' => array_merge($normalPokemon, $hiddenPokemon),
			]
		);

		return new HtmlResponse($content);
	}
}
