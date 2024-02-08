<?php

namespace S4mpp\Laraguard\Helpers;

class FieldUsername
{
	public function __construct(private string $title, private string $field)
	{}

	public function getField(): string
	{
		return $this->field;
	}

	public function getTitle(): string
	{
		return $this->title;
	}
}