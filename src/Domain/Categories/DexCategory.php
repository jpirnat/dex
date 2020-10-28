<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Categories;

final class DexCategory
{
	public function __construct(
		private string $icon,
		private string $name,
	) {}

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
