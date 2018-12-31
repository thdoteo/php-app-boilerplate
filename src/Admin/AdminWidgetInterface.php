<?php

namespace App\Admin;

interface AdminWidgetInterface
{

    public function render(): string;

    public function getPosition(): int;

    public function renderMenu(): string;
}
