<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TextLinks;

final readonly class TextLinkMove
{
	public function __construct(
		private string $vgIdentifier,
		private string $moveIdentifier,
		private string $moveName,
	) {}

	public function getLinkHtml() : string
	{
		$vgIdentifier = $this->vgIdentifier;
		$moveIdentifier = $this->moveIdentifier;
		$moveName = $this->moveName;
		return "<a class=\"dex-link\" href=\"/dex/$vgIdentifier/moves/$moveIdentifier\">$moveName</a>";
	}
}
