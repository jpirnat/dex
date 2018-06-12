<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

use Jp\Dex\Application\Models\LeadsAveraged\LeadsAveragedModel;
use Jp\Dex\Application\Models\LeadsAveraged\LeadsData;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use Zend\Diactoros\Response;

class LeadsAveragedView
{
	/** @var Twig_Environment $twig */
	private $twig;

	/** @var BaseView $baseView */
	private $baseView;

	/** @var LeadsAveragedModel $leadsAveragedModel */
	private $leadsAveragedModel;

	/** @var IntlFormatterFactory $formatterFactory */
	private $formatterFactory;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $twig
	 * @param BaseView $baseView
	 * @param LeadsAveragedModel $leadsAveragedModel
	 * @param IntlFormatterFactory $formatterFactory
	 */
	public function __construct(
		Twig_Environment $twig,
		BaseView $baseView,
		LeadsAveragedModel $leadsAveragedModel,
		IntlFormatterFactory $formatterFactory
	) {
		$this->twig = $twig;
		$this->baseView = $baseView;
		$this->leadsAveragedModel = $leadsAveragedModel;
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * Get leads data averaged over multiple months.
	 *
	 * @return ResponseInterface
	 */
	public function getData() : ResponseInterface
	{
		$start = $this->leadsAveragedModel->getStart();
		$end = $this->leadsAveragedModel->getEnd();
		$formatIdentifier = $this->leadsAveragedModel->getFormatIdentifier();
		$rating = $this->leadsAveragedModel->getRating();

		$formatter = $this->formatterFactory->createFor(
			$this->leadsAveragedModel->getLanguageId()
		);

		// Get usage data and sort by rank.
		$leadsDatas = $this->leadsAveragedModel->getLeadsDatas();
		uasort(
			$leadsDatas,
			function (LeadsData $a, LeadsData $b) : int {
				return $a->getRank() <=> $b->getRank();
			}
		);

		// Compile all usage data into the right form.
		$data = [];
		foreach ($leadsDatas as $leadsData) {
			$data[] = [
				'rank' => $leadsData->getRank(),
				'name' => $leadsData->getPokemonName(),
				'showMovesetLink' => $leadsData->getMonths() > 0,
				'identifier' => $leadsData->getPokemonIdentifier(),
				'formIcon' => $leadsData->getFormIcon(),
				'usagePercent' => $formatter->formatPercent($leadsData->getLeadUsagePercent()),
				'raw' => $formatter->formatNumber($leadsData->getRaw()),
				'rawPercent' => $formatter->formatPercent($leadsData->getRawPercent()),
			];
		}

		// Navigation breadcrumbs.
		$breadcrumbs = [
			[
				'url' => '/stats',
				'text' => 'Stats',
			],
			[
				'text' => 'Formats',
			],
			[
				'url' => "/stats/$start-to-$end/$formatIdentifier/$rating",
				'text' => 'Usage',
			],
			[
				'text' => 'Leads',
			]
		];

		$content = $this->twig->render(
			'html/leads-averaged.twig',
			$this->baseView->getBaseVariables() + [
				'breadcrumbs' => $breadcrumbs,

				'formatIdentifier' => $formatIdentifier,
				'rating' => $rating,

				'start' => $start,
				'end' => $end,

				// The main data.
				'data' => $data,
			]
		);

		$response = new Response();
		$response->getBody()->write($content);
		return $response;
	}
}
