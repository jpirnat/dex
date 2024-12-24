<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Teammates;

final readonly class StatsPokemonTeammate
{
	public function __construct(
		private(set) string $icon,
		private(set) string $identifier,
		private(set) string $name,
		private(set) float $percent,
	) {}
}
