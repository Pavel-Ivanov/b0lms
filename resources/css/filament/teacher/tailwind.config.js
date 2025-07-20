import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
/*
        './app/Filament/Teacher/!**!/!*.php',
        './resources/views/filament/teacher/!**!/!*.blade.php',
        './vendor/filament/!**!/!*.blade.php',
*/
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './resources/views/infolists/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './vendor/filament/**/*.blade.php',

    ],
}
