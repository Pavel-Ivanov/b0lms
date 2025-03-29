import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Sadmin/**/*.php',
        './resources/views/filament/sadmin/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/matinee/resources/views/**/*.blade.php',
    ],
}
