<?php

namespace S4mpp\Laraguard;

use Illuminate\Support\Facades\Lang;

final class Utils
{
    public static function getSegmentRouteName(int $path_step, ?string $current_route = null): ?string
    {
        if (! $current_route) {
            $current_route = request()?->route()?->getAction('as');
        }

        $path_steps = explode('.', $current_route);

        return $path_steps[$path_step] ?? null;
    }

    /**
     * @param  array<mixed>  $replace
     */
    public static function translate(string $key, array $replace = []): string
    {
        if (! Lang::has($key)) {
            return $key;
        }

        $str_translated = Lang::get($key, $replace);

        if (is_array($str_translated)) {
            return 'TRANSLATION ARRAY: '.json_encode($str_translated);
        }

        return $str_translated;
    }
}
