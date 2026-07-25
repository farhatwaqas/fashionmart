@echo off
title Fashion Corner (Laravel)
cd /d "%~dp0"

set PATH=D:\laragon\bin\php\php-8.2.29-nts-Win32-vs16-x64;D:\laragon\bin\composer;D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin;%PATH%

echo.
echo Fashion Corner — Laravel
echo Store:  http://127.0.0.1:8000
echo Admin:  http://127.0.0.1:8000/admin
echo Login:  admin@fashioncorner.test / password
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause
