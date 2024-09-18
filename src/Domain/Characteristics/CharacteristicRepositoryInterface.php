<?php
declare(strict_types=1);


namespace Jp\Dex\Domain\Characteristics;

interface CharacteristicRepositoryInterface
{
	/**
	 * Get a characteristic by its identifier.
	 *
	 * @throws CharacteristicNotFoundException if no characteristic exists with
	 *     this identifier.
	 */
	public function getByIdentifier(string $identifier) : Characteristic;
}
