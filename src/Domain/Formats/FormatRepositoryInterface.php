<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Formats;

use Exception;

interface FormatRepositoryInterface
{
	/**
	 * Get a format by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no format exists with this identifier.
	 *
	 * @return Format
	 */
	public function getByIdentifier(string $identifier) : Format;
}
