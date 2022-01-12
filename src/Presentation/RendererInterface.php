<?php
declare(strict_types=1);

namespace Jp\Dex\Presentation;

interface RendererInterface
{
	/**
	 * Render the template with this data.
	 */
	public function render(string $template, array $data = []) : string;
}
