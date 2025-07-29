@echo off
echo 🔧 Fixing Cache Issue for Windows...

REM Step 1: Clear all cache files
echo 🧹 Clearing all cache...
if exist "bootstrap\cache\*.php" del /q "bootstrap\cache\*.php"
if exist "storage\framework\cache\data\*" del /q /s "storage\framework\cache\data\*"
if exist "storage\framework\sessions\*" del /q /s "storage\framework\sessions\*"
if exist "storage\framework\views\*" del /q /s "storage\framework\views\*"

REM Step 2: Clear Laravel cache
echo 🧹 Clearing Laravel cache...
php artisan cache:clear 2>nul || echo Cache clear skipped
php artisan config:clear 2>nul || echo Config clear skipped
php artisan route:clear 2>nul || echo Route clear skipped
php artisan view:clear 2>nul || echo View clear skipped

REM Step 3: Regenerate autoload
echo 🔄 Regenerating autoload...
composer dump-autoload --optimize

REM Step 4: Try package discovery
echo 📦 Running package discovery...
php artisan package:discover --ansi

echo ✅ Cache fix completed!
