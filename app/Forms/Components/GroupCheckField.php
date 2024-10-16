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

    protected string | Closure $type = '';

    public $checkIns = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);
        // $this->itemLink('ok');


        $this->afterStateHydrated(static function (GroupCheckField $component, $state) {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

    }

    public function type(string | Closure $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->evaluate($this->type) ?? '';
    }
}
