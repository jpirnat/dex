<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilityModel;
use Jp\Dex\Application\Models\Structs\DexPokemon;
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
	 * Show the /dex/abilities/ability-identifier page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$ability = $this->dexAbilityModel->getAbility();

		$normalPokemon = $this->dexAbilityModel->getNormalPokemon();
		$hiddenPokemon = $this->dexAbilityModel->getHiddenPokemon();

		$normalPokemon = $this->dexFormatter->formatDexPokemon($normalPokemon);
		$hiddenPokemon = $this->dexFormatter->formatDexPokemon($hiddenPokemon);
		// TODO: Sort Pokémon somehow. Add a sort value to class Pokemon?

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'url' => '/dex/abilities',
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
				'ability' => $ability,
				'stats' => ['HP', 'Atk', 'Def', 'SpA', 'SpD', 'Spe', 'BST'],
				// TODO: Pull these stat names from somewhere else.
				'pokemon' => array_merge($normalPokemon, $hiddenPokemon),
			]
		);

		return new HtmlResponse($content);
	}
}
