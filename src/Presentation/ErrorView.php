<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\BaseModel;
use Jp\Dex\Application\Models\ErrorModel;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class ErrorView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var ErrorModel $errorModel */
	private $errorModel;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param ErrorModel $errorModel
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		ErrorModel $errorModel
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->errorModel = $errorModel;
	}

	/**
	 * Get the Error page.
	 *
	 * @return ResponseInterface
	 */
	public function getError() : ResponseInterface
	{
		$breadcrumbs = [
			[
				'text' => '???',
			],
		];

		$content = $this->twig->render(
			'html/error.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'An error has occurred!',
				'breadcrumbs' => $breadcrumbs,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);

		return $response;
	}
}
