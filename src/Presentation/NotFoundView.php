<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\NotFoundModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;

class NotFoundView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var NotFoundModel $notFoundModel */
	private $notFoundModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param NotFoundModel $notFoundModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		NotFoundModel $notFoundModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->notFoundModel = $notFoundModel;
	}

	/**
	 * Get the 404 Not Found page.
	 *
	 * @return ResponseInterface
	 */
	public function get404() : ResponseInterface
	{
		$breadcrumbs = [
			[
				'text' => '???',
			],
		];

		$content = $this->twig->render(
			'html/404.twig',
			$this->baseView->getBaseVariables() + [
				'title' => '404 Not Found',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		return new HtmlResponse($content);
	}
}
