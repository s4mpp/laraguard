<?php

namespace S4mpp\Laraguard\Traits;

trait HasGuard
{
    private function _getGuard(): string
    {
        return $this->guard ?? 'web';
    }
}