<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Controllers;

use Jp\Dex\Application\Models\AdvancedPokemonSearch\AdvancedPokemonSearchIndexModel;
use Jp\Dex\Domain\Languages\LanguageId;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AdvancedPokemonSearchIndexController
{
	public function __construct(
		private BaseController $baseController,
		private AdvancedPokemonSearchIndexModel $advancedPokemonSearchIndexModel,
	) {}

	/**
	 * Set data for the advanced Pokémon search page.
	 */
	public function setData(ServerRequestInterface $request) : void
	{
		$this->baseController->setBaseVariables($request);

		$vgIdentifier = $request->getAttribute('vgIdentifier');
		$languageId = new LanguageId((int) $request->getAttribute('languageId'));

		$this->advancedPokemonSearchIndexModel->setData($vgIdentifier, $languageId);
	}
}
