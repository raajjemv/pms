<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Arrayable;
use Filament\Forms\Components\Concerns\HasOptions;

class GroupCheckField extends Field
{
    use HasOptions;

    protected string $view = 'forms.components.group-check-field';

    public $checkIns = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);


        $this->afterStateHydrated(static function (GroupCheckField $component, $state) {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->registerActions([
            fn($component) => $component->mediaPicker()
        ]);
    }
}
