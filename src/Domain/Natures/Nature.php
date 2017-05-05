<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Natures;

use Jp\Dex\Domain\Stats\StatId;

class Nature
{
	/** @var NatureId $id */
	private $id;

	/** @var string $identifier */
	private $identifier;

	/** @var StatId|null $increasedStatId */
	private $increasedStatId;

	/** @var StatId|null $decreasedStatId */
	private $decreasedStatId;

	/**
	 * Constructor.
	 *
	 * @param NatureId $natureId
	 * @param string $identifier
	 * @param StatId|null $increasedStatId
	 * @param StatId|null $decreasedStatId
	 */
	public function __construct(
		NatureId $natureId,
		string $identifier,
		?StatId $increasedStatId,
		?StatId $decreasedStatId
	) {
		$this->id = $natureId;
		$this->identifier = $identifier;
		$this->increasedStatId = $increasedStatId;
		$this->decreasedStatId = $decreasedStatId;
	}

	/**
	 * Get the nature's id.
	 *
	 * @return NatureId
	 */
	public function id() : NatureId
	{
		return $this->id;
	}

	/**
	 * Get the nature's identifier.
	 *
	 * @return string
	 */
	public function identifier() : string
	{
		return $this->identifier;
	}

	/**
	 * Get the nature's increased stat id.
	 *
	 * @return StatId|null
	 */
	public function increasedStatId() : ?StatId
	{
		return $this->increasedStatId;
	}

	/**
	 * Get the nature's decreased stat id.
	 *
	 * @return StatId|null
	 */
	public function decreasedStatId() : ?StatId
	{
		return $this->decreasedStatId;
	}
}
