<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Jp\Dex\Domain\EntityId;

final class FormatId extends EntityId
{
	public const GEN_7_AG = 36;
	public const GEN_7_UBERS = 37;
	public const GEN_7_DOUBLES_UBERS = 45;
	public const VGC_2019_SUN = 50;
	public const VGC_2019_MOON = 51;
	public const VGC_2019_ULTRA = 52;

	public const GEN_9_OU = 70;
}
