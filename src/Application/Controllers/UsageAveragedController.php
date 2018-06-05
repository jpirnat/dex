<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\UsageAveraged\UsageAveragedModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

class UsageAveragedController
{
	/** @var BaseController $baseController */
	private $baseController;

	/** @var UsageAveragedModel $usageAveragedModel */
	private $usageAveragedModel;

	/**
	 * Constructor.
	 *
	 * @param BaseController $baseController
	 * @param UsageAveragedModel $usageAveragedModel
	 */
	public function __construct(
		BaseController $baseController,
		UsageAveragedModel $usageAveragedModel
	) {
		$this->baseController = $baseController;
		$this->usageAveragedModel = $usageAveragedModel;
	}

	/**
	 * Get usage data averaged over multiple months.
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return void
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$start = $request->getAttribute('start');
		$end = $request->getAttribute('end');
		$formatIdentifier = $request->getAttribute('formatIdentifier');
		$rating = (int) $request->getAttribute('rating');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->usageAveragedModel->setData(
			$start,
			$end,
			$formatIdentifier,
			$rating,
			$languageId
		);
	}
}
