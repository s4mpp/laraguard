<?php

namespace S4mpp\Laraguard;

use S4mpp\Laraguard\Base\Panel;


class Laraguard
{
	private static $panels = [];

	public static function panel(string $title, string $prefix = '', string $guard = 'web'): Panel
	{
		$panel = new Panel($title, $prefix, $guard);
		
		self::$panels[$guard] = $panel;

		return $panel;
	}

	public static function getPanels(): array
	{
		return self::$panels;
	}

	public static function getPanel(string $guard_name): ?Panel
	{
		return self::$panels[$guard_name] ?? null;
	}

	public static function layout(string $view = null, array $data = [])
	{
		return self::getPanel(Panel::current())->getLayout($view, $data);
	}
}