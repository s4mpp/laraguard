<?php

namespace S4mpp\Laraguard;

use Illuminate\Contracts\View\View;
use S4mpp\Laraguard\Base\Panel;


class Laraguard
{
	private static array $panels = [];

	public static function panel(string $title, string $prefix = '', string $guard = 'web'): Panel
	{
		$panel = new Panel($title, $prefix, $guard);
		
		self::$panels[$guard] = $panel;

		return $panel;
	}

	/**
	 *
	 * @return array<Panel>
	 */
	public static function getPanels(): array
	{
		return self::$panels;
	}

	public static function getPanel(string $guard_name): ?Panel
	{
		return self::$panels[$guard_name] ?? null;
	}

	public static function layout(string $view = null, array $data = []): View
	{
		return self::getPanel(Panel::current())->getLayout($view, $data);
	}
}