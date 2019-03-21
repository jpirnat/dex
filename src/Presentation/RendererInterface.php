<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

interface RendererInterface
{
	/**
	 * Render the template with this data.
	 *
	 * @param string $template
	 * @param array $data
	 *
	 * @return string
	 */
	public function render(string $template, array $data = []) : string;
}
