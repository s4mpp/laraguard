<?php

namespace S4mpp\Laraguard;

class Laraguard
{
	private static $guards = [];

	public static function create(string $title, string $guard)
	{
		$guard = new Guard($title, $guard);

		self::$guards[] = $guard;

		return $guard;
	}

	public static function getGuards(): array
	{
		return self::$guards;
	}
}