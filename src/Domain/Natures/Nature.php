<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Stats\StatId;

final class Nature
{
	/** @var NatureId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var StatId|null $increasedStatId */
	private $increasedStatId;

	/** @var StatId|null $decreasedStatId */
	private $decreasedStatId;

	/** @var int $vcExpRemainder */
	private $vcExpRemainder;

	/**
	 * Constructor.
	 *
	 * @param NatureId $natureId
	 * @param string $identifier
	 * @param StatId|null $increasedStatId
	 * @param StatId|null $decreasedStatId
	 * @param int $vcExpRemainder
	 */
	public function __construct(
		NatureId $natureId,
		string $identifier,
		?StatId $increasedStatId,
		?StatId $decreasedStatId,
		int $vcExpRemainder
	) {
		$this->id = $natureId;
		$this->identifier = $identifier;
		$this->increasedStatId = $increasedStatId;
		$this->decreasedStatId = $decreasedStatId;
		$this->vcExpRemainder = $vcExpRemainder;
	}

	/**
	 * Get the nature's id.
	 *
	 * @return NatureId
	 */
	public function getId() : NatureId
	{
		return $this->id;
	}

	/**
	 * Get the nature's identifier.
	 *
	 * @return string
	 */
	public function getIdentifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the nature's increased stat id.
	 *
	 * @return StatId|null
	 */
	public function getIncreasedStatId() : ?StatId
	{
		return $this->increasedStatId;
	}

	/**
	 * Get the nature's decreased stat id.
	 *
	 * @return StatId|null
	 */
	public function getDecreasedStatId() : ?StatId
	{
		return $this->decreasedStatId;
	}

	/**
	 * Get the nature's Virtual Console experience points remainder.
	 *
	 * @return int
	 */
	public function getVcExpRemainder() : int
	{
		return $this->vcExpRemainder;
	}
}
