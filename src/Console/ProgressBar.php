<?php

namespace CollabCorp\LaravelImmigrations\Console;

use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProgressBar
{
    public static function create(int $max, SymfonyStyle $console): SymfonyProgressBar
    {
        return tap($console->createProgressBar($max), function (SymfonyProgressBar $progressBar) {
            $progressBar->setBarCharacter(config('immigrations.progress.bar_character', '#'));
            $progressBar->setProgressCharacter(config('immigrations.progress.progress_character', '->'));
            $progressBar->setFormat(config('immigrations.progress.format', 'debug'));
            $progressBar->setBarWidth(100);
        });
    }
}
