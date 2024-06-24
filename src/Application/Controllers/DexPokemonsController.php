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
	 * Show the dex Pokémons page.
	 */
	public function index(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->dexPokemonsModel->setData($vgIdentifier, $languageId);
	}
}
