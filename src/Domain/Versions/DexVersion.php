<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final class DexVersion
{
	public function __construct(
		private string $abbreviation,
		private string $backgroundColor,
		private string $textColor,
	) {}

	public function getAbbreviation() : string
	{
		return $this->abbreviation;
	}

	public function getBackgroundColor() : string
	{
		return $this->backgroundColor;
	}

	public function getTextColor() : string
	{
		return $this->textColor;
	}
}
