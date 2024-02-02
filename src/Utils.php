<?php

namespace S4mpp\Laraguard;


class Utils
{
	public static function getSegmentRouteName(int $path_step, string $route): ?string
	{
		$path_steps = explode('.', $route);
		
		return $path_steps[$path_step] ?? null;
	}
}