<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class DexAbilitiesView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexAbilitiesModel $dexAbilitiesModel */
	private $dexAbilitiesModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param DexAbilitiesModel $dexAbilitiesModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		DexAbilitiesModel $dexAbilitiesModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->dexAbilitiesModel = $dexAbilitiesModel;
	}

	/**
	 * Show the /dex/abilities page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexAbilitiesModel->getGenerationModel();
		$generationIdentifier = $generationModel->getGeneration()->getIdentifier();

		$abilities = $this->dexAbilitiesModel->getAbilities();

		uasort($abilities, function (array $a, array $b) : int {
			return $a['name'] <=> $b['name'];
		});

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Abilities',
			],
		];

		$content = $this->twig->render(
			'html/dex/abilities.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Abilities',
				'breadcrumbs' => $breadcrumbs,
				'generationIdentifier' => $generationIdentifier,
				'abilities' => $abilities,
			]
		);

		return new HtmlResponse($content);
	}
}
