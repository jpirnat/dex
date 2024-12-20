<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\EntityId;

final class FormatId extends EntityId
{
	public const int GEN_7_UBERS = 35;
	public const int GEN_7_DOUBLES_UBERS = 43;
	public const int VGC_2019_SUN = 48;
	public const int VGC_2019_MOON = 49;
	public const int VGC_2019_ULTRA = 50;

	public const int GEN_9_OU = 66;
}
