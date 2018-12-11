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

	/** @var DexFormatter $dexFormatter */
	private $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param DexAbilitiesModel $dexAbilitiesModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		DexAbilitiesModel $dexAbilitiesModel,
		DexFormatter $dexFormatter
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->dexAbilitiesModel = $dexAbilitiesModel;
		$this->dexFormatter = $dexFormatter;
	}

	/**
	 * Show the dex abilities page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexAbilitiesModel->getGenerationModel();
		$generation = $generationModel->getGeneration();
		$generations = $generationModel->getGenerations();

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
				'generation' => [
					'identifier' => $generation->getIdentifier(),
				],
				'title' => 'Abilities',
				'breadcrumbs' => $breadcrumbs,
				'generations' => $this->dexFormatter->formatGenerations($generations),
				'abilities' => $abilities,
			]
		);

		return new HtmlResponse($content);
	}
}
