<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Presentation\RendererInterface;
use Twig\Environment;

final class TwigRenderer implements RendererInterface
{
	public function __construct(
		private Environment $twig,
	) {}

	/**
	 * Render the template with this data.
	 *
	 * @param string $template
	 * @param array $data
	 *
	 * @return string
	 */
	public function render(string $template, array $data = []) : string
	{
		return $this->twig->render($template, $data);
	}
}
