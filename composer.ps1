# Run Composer via local/global phar without PHP 8.4 deprecation noise.
$composerPhar = Join-Path (Split-Path (Get-Command composer -ErrorAction SilentlyContinue).Source -Parent) "composer.phar"
if (-not (Test-Path $composerPhar)) {
    $composerPhar = "C:\composer\composer.phar"
}
$prepend = Join-Path $PSScriptRoot "bootstrap\suppress_deprecations.php"
php -d "auto_prepend_file=$prepend" $composerPhar @args
