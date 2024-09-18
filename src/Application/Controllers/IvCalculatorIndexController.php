<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\IvCalculator\IvCalculatorIndexModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class IvCalculatorIndexController
{
	public function __construct(
		private BaseController $baseController,
		private IvCalculatorIndexModel $ivCalculatorIndexModel,
	) {}

	/**
	 * Set data for the IV calculator page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->ivCalculatorIndexModel->setData($vgIdentifier, $languageId);
	}
}
