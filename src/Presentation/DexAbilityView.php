<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilityModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class DexAbilityView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexAbilityModel $dexAbilityModel */
	private $dexAbilityModel;

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param DexAbilityModel $dexAbilityModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		DexAbilityModel $dexAbilityModel,
		DexFormatter $dexFormatter
	) {
		$this->twig = $twig;
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

		$ability = $this->dexAbilityModel->getAbility();

		$normalPokemon = $this->dexAbilityModel->getNormalPokemon();
		$hiddenPokemon = $this->dexAbilityModel->getHiddenPokemon();

		$normalPokemon = $this->dexFormatter->formatDexPokemon($normalPokemon);
		$hiddenPokemon = $this->dexFormatter->formatDexPokemon($hiddenPokemon);
		// TODO: Sort Pokémon somehow. Add a sort value to class Pokemon?

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

		$content = $this->twig->render(
			'html/dex/ability.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Abilities - ' . $ability['name'],
				'breadcrumbs' => $breadcrumbs,
				'generation' => [
					'id' => $generation->getId()->value(),
					'identifier' => $generation->getIdentifier(),
				],
				'ability' => $ability,
				'stats' => ['HP', 'Atk', 'Def', 'SpA', 'SpD', 'Spe', 'BST'],
				// TODO: Pull these stat names from somewhere else.
				'pokemons' => array_merge($normalPokemon, $hiddenPokemon),
			]
		);

		return new HtmlResponse($content);
	}
}
