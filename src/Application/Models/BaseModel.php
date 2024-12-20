<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models;

use Jp\Dex\Domain\Languages\LanguageId;

final class BaseModel
{
	private(set) LanguageId $currentLanguageId;

	/**
	 * Set data for the base model.
	 */
	public function setData(LanguageId $currentLanguageId) : void
	{
		$this->currentLanguageId = $currentLanguageId;
	}

	/**
	 * Get the language names, for the language select in the footer.
	 */
	public function getLanguages() : array
	{
		// I'm hard-coding it for the sake of one fewer database trip on every page load.
		// Plus, this list only changes once every 3 to 6 years.
		return [
			['id' => 4, 'name' => 'Deutsch'],
			['id' => 2, 'name' => 'English'],
			['id' => 6, 'name' => 'Español'],
			['id' => 3, 'name' => 'Français'],
			['id' => 5, 'name' => 'Italiano'],
			['id' => 1, 'name' => 'にほんご'],
			['id' => 8, 'name' => '漢字'],
			['id' => 9, 'name' => '简体中文'],
			['id' => 10, 'name' => '繁體中文'],
			['id' => 7, 'name' => '한국어'],
		];
	}
}
