<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Evolutions;

final readonly class EvolutionTableMethod
{
	public function __construct(
		private string $html,
	) {}

	public function getHtml() : string
	{
		return $this->html;
	}
}
