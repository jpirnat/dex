<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats;

class Nature
{
	/** @var NatureId $id */
	protected $id;

	/** @var string $identifier */
	protected $identifier;

	/** @var StatId|null $increasedStatId */
	protected $increasedStatId;

	/** @var StatId|null $decreasedStatId */
	protected $decreasedStatId;

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
