<?php

namespace S4mpp\Laraguard\Helpers;

final class Layout
{
    public function __construct(
        private string $html = 'laraguard::html',
        private string $auth = 'laraguard::auth.main',
        private string $layout = 'laraguard::restricted-area'
    ) {
    }

    public function getHtmlFile(): string
    {
        return $this->html;
    }

    public function getAuthFile(): string
    {
        return $this->auth;
    }

    public function getBodyFile(): string
    {
        return $this->layout;
    }

    public function setHtmlFile(string $file): self
    {
        $this->html = $file;

        return $this;
    }

    public function setAuthFile(string $file): self
    {
        $this->auth = $file;

        return $this;
    }

    public function setLayoutFile(string $file): self
    {
        $this->layout = $file;

        return $this;
    }
}
