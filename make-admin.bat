@echo off
echo ========================================
echo ResQHub - Make User Admin
echo ========================================
echo.

set /p EMAIL="Enter user email: "

echo.
echo Making %EMAIL% an admin...
C:\xampp\mysql\bin\mysql.exe -u root resqhub -e "UPDATE users SET role = 'admin' WHERE email = '%EMAIL%';"

if %errorlevel% equ 0 (
    echo.
    echo ✅ Success! %EMAIL% is now an admin!
    echo.
    echo Next steps:
    echo 1. Logout and login again
    echo 2. Go to: http://localhost:8000/admin/disasters
    echo.
) else (
    echo.
    echo ❌ Error: Could not update user
    echo Make sure the email exists in the database
    echo.
)

pause
