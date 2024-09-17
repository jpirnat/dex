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
		$level = (string) ($data['level'] ?? '100');
		$natureIdentifier = $data['natureIdentifier'] ?? '';
		$ivs = $data['ivs'] ?? [];
		$finalStats = $data['finalStats'] ?? [];

		$this->evCalculatorSubmitModel->setData(
			$vgIdentifier,
			$pokemonIdentifier,
			$level,
			$natureIdentifier,
			$ivs,
			$finalStats,
		);
	}
}
