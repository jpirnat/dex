<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\DexNaturesModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class DexNaturesView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var DexNaturesModel $dexNaturesModel */
	private $dexNaturesModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param DexNaturesModel $dexNaturesModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		DexNaturesModel $dexNaturesModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->dexNaturesModel = $dexNaturesModel;
	}

	/**
	 * Show the dex natures page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		// Navigational breadcrumbs.
		$breadcrumbs = [
			[
				'text' => 'Dex',
			],
			[
				'text' => 'Natures',
			],
		];

		$content = $this->twig->render(
			'html/dex/natures.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Natures',
				'breadcrumbs' => $breadcrumbs,
				'natures' => $this->dexNaturesModel->getNatures(),
			]
		);

		return new HtmlResponse($content);
	}
}
