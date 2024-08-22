<?php

namespace App\Filament;

use Illuminate\Support\Str;
use Swis\Filament\Backgrounds\Image;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Swis\Filament\Backgrounds\Contracts\ProvidesImages;

class LoginBackground implements ProvidesImages
{
    protected string $directory;

    public static function make(): static
    {
        return app(static::class);
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function getImage(): Image
    {
        if (!isset($this->directory)) {
            throw new \RuntimeException('No image directory set, please provide a directory using the directory() method.');
        }

       
        return new Image(
            'url("' . asset('backgrounds/unsplash.jpg') . '")'
        );
    }
}
