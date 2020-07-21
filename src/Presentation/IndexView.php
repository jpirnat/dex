<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;

final class IndexView
{
	private RendererInterface $renderer;
	private BaseView $baseView;

	/**
	 * Constructor.
	 *
	 * @param RendererInterface $renderer
	 * @param BaseView $baseView
	 */
	public function __construct(RendererInterface $renderer, BaseView $baseView)
	{
		$this->renderer = $renderer;
		$this->baseView = $baseView;
	}

	/**
	 * Show the home page.
	 *
	 * @return ResponseInterface
	 */
	public function index() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/index.twig',
			$this->baseView->getBaseVariables()
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex abilities page.
	 *
	 * @return ResponseInterface
	 */
	public function dexAbilities() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/abilities.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Abilities',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex moves page.
	 *
	 * @return ResponseInterface
	 */
	public function dexMoves() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/moves.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Moves',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex natures page.
	 *
	 * @return ResponseInterface
	 */
	public function dexNatures() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/natures.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Natures',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the dex types page.
	 *
	 * @return ResponseInterface
	 */
	public function dexTypes() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/dex/types.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Types',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats usage page.
	 *
	 * @return ResponseInterface
	 */
	public function statsUsage() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/usage.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats leads page.
	 *
	 * @return ResponseInterface
	 */
	public function statsLeads() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/leads.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats ability page.
	 *
	 * @return ResponseInterface
	 */
	public function statsAbility() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/ability.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats item page.
	 *
	 * @return ResponseInterface
	 */
	public function statsItem() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/item.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats move page.
	 *
	 * @return ResponseInterface
	 */
	public function statsMove() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/move.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the stats chart page.
	 *
	 * @return ResponseInterface
	 */
	public function statsChart() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/stats/chart.twig',
			$this->baseView->getBaseVariables() + [
				'title' => 'Stats - Chart',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the 404 page.
	 *
	 * @return ResponseInterface
	 */
	public function error404() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/404.twig',
			$this->baseView->getBaseVariables() + [
				'title' => '404 Not Found',
			]
		);

		return new HtmlResponse($content);
	}

	/**
	 * Show the error page.
	 *
	 * @return ResponseInterface
	 */
	public function error() : ResponseInterface
	{
		$content = $this->renderer->render(
			'html/error.twig',
			$this->baseView->getBaseVariables() + [
				'title' => '404 Not Found',
			]
		);

		return new HtmlResponse($content);
	}
}
