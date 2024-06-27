<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\DexPokemonsModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DexPokemonsController
{
	public function __construct(
		private BaseController $baseController,
		private DexPokemonsModel $dexPokemonsModel,
	) {}

	/**
	 * Set data for the dex Pokémons page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexPokemonsModel->setData($vgIdentifier, $languageId);
	}
}
