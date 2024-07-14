<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\TextLinks;

final readonly class TextLinkType
{
	public function __construct(
		private string $vgIdentifier,
		private string $typeIdentifier,
		private string $typeName,
	) {}

	public function getLinkHtml() : string
	{
		$vgIdentifier = $this->vgIdentifier;
		$typeIdentifier = $this->typeIdentifier;
		$typeName = $this->typeName;
		return "<a class=\"dex-link\" href=\"/dex/$vgIdentifier/types/$typeIdentifier\">$typeName</a>";
	}
}
