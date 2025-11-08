@echo off
echo ========================================
echo ResQHub Setup Script
echo ========================================
echo.

echo [1/6] Installing Composer dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed
    pause
    exit /b 1
)
echo.

echo [2/6] Installing NPM dependencies...
call npm install
if %errorlevel% neq 0 (
    echo ERROR: NPM install failed
    pause
    exit /b 1
)
echo.

echo [3/6] Creating database...
echo Please ensure MySQL is running and create the database manually:
echo   mysql -u root -p
echo   CREATE DATABASE resqhub;
echo   EXIT;
echo.
pause

echo [4/6] Running migrations...
call php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Migration failed
    pause
    exit /b 1
)
echo.

echo [5/6] Building frontend assets...
call npm run build
if %errorlevel% neq 0 (
    echo ERROR: Build failed
    pause
    exit /b 1
)
echo.

echo [6/6] Fetching initial earthquake data...
call php artisan earthquakes:fetch
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo To start the application, run:
echo   php artisan serve
echo.
echo Or use the development server:
echo   composer dev
echo.
echo Then open http://localhost:8000 in your browser
echo.
pause
