<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Versions;

final readonly class DexVersion
{
	public function __construct(
		private string $abbreviation,
	) {}

	public function getAbbreviation() : string
	{
		return $this->abbreviation;
	}
}
