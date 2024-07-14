<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TextLinks;

final readonly class TextLinkPokemon
{
	public function __construct(
		private string $vgIdentifier,
		private string $pokemonIdentifier,
		private string $pokemonName,
	) {}

	public function getLinkHtml() : string
	{
		$vgIdentifier = $this->vgIdentifier;
		$pokemonIdentifier = $this->pokemonIdentifier;
		$pokemonName = $this->pokemonName;
		return "<a class=\"dex-link\" href=\"/dex/$vgIdentifier/pokemon/$pokemonIdentifier\">$pokemonName</a>";
	}
}
