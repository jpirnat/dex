<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TextLinks;

final readonly class TextLinkItem
{
	public function __construct(
		private string $vgIdentifier,
		private string $itemIdentifier,
		private string $itemName,
	) {}

	public function getLinkHtml() : string
	{
		$vgIdentifier = $this->vgIdentifier;
		$itemIdentifier = $this->itemIdentifier;
		$itemName = $this->itemName;
		return "<a class=\"dex-link\" href=\"/dex/$vgIdentifier/items/$itemIdentifier\">$itemName</a>";
	}
}
