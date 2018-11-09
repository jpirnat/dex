<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexTypesModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class DexTypesView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexTypesModel $dexTypesModel */
	private $dexTypesModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param DexTypesModel $dexTypesModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		DexTypesModel $dexTypesModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->dexTypesModel = $dexTypesModel;
	}

	/**
	 * Show the dex types page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$generationModel = $this->dexTypesModel->getGenerationModel();
		$generationIdentifier = $generationModel->getGeneration()->getIdentifier();

		$types = $this->dexTypesModel->getTypes();
		$factors = $this->dexTypesModel->getFactors();

		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Types',
			],
		];

		$content = $this->twig->render(
			'html/dex/types.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Types',
				'breadcrumbs' => $breadcrumbs,
				'generationIdentifier' => $generationIdentifier,
				'types' => $types,
				'factors' => $factors,
			]
		);

		return new HtmlResponse($content);
	}
}
