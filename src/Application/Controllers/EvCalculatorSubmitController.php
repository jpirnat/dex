<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\EvCalculator\EvCalculatorSubmitModel;
use Psr\Http\Message\ServerRequestInterface;

final readonly class EvCalculatorSubmitController
{
	public function __construct(
		private EvCalculatorSubmitModel $evCalculatorSubmitModel,
	) {}

	/**
	 * Set data for the EV calculator page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$body = $request->getBody()->getContents();
		$data = json_decode($body, true);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$pokemonIdentifier = $data['pokemonIdentifier'] ?? '';
		$natureIdentifier = (string) ($data['natureIdentifier'] ?? '') ?: 'hardy';
		$ivs = $data['ivs'] ?? [];
		$atLevel = (array) ($data['atLevel'] ?? []);

		$this->evCalculatorSubmitModel->setData(
			$vgIdentifier,
			$pokemonIdentifier,
			$natureIdentifier,
			$ivs,
			$atLevel,
		);
	}
}
