@echo off
echo Menjalankan Laravel backend...
start cmd /k "cd /d D:\Program\Document\GitHub\Stoir && php artisan serve"

echo Menjalankan React frontend...
start cmd /k "cd /d D:\Program\Document\GitHub\Stoir\frontend && npm start"

echo Semua service sudah dijalankan!
pause
