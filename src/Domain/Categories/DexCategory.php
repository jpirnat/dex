<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

class DexCategory
{
	/** @var string $icon */
	private $icon;

	/** @var string $name */
	private $name;

	/**
	 * Constructor.
	 *
	 * @param string $icon
	 * @param string $name
	 */
	public function __construct(string $icon, string $name)
	{
		$this->icon = $icon;
		$this->name = $name;
	}

	/**
	 * Get the category's icon.
	 *
	 * @return string
	 */
	public function getIcon() : string
	{
		return $this->icon;
	}

	/**
	 * Get the category's name.
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
