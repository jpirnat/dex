<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexAbilitiesModel;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class DexAbilitiesView
{
	private RendererInterface $renderer;
	private BaseView $baseView;
	private DexAbilitiesModel $dexAbilitiesModel;
	private DexFormatter $dexFormatter;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 * @param DexAbilitiesModel $dexAbilitiesModel
	 * @param DexFormatter $dexFormatter
	 */
	public function __construct(
		RendererInterface $renderer,
		BaseView $baseView,
		DexAbilitiesModel $dexAbilitiesModel,
		DexFormatter $dexFormatter
	) {
		$this->renderer = $renderer;
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

		$content = $this->renderer->render(
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
