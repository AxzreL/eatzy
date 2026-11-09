@echo off
title EATZY - 1-Click Sync to Vercel
cls
echo ==========================================================
echo        EATZY: Syncing Joomla CMS to Vercel Cloud
echo ==========================================================
echo.

echo [1/3] Fetching latest menu, dishes and reservations from local Joomla...
C:\xampp\php\php.exe tools\sync_from_joomla.php
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] Failed to fetch content from Joomla.
    echo Please make sure Apache and MySQL are running in XAMPP!
    echo.
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo [2/3] Staging changes and committing to Git...
git add .
git diff-index --quiet HEAD || git commit -m "Update website content from local Joomla CMS"

echo.
echo [3/3] Pushing to GitHub (triggers instant Vercel redeployment)...
git push origin main
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] Failed to push to GitHub. Please check your internet connection or git login.
    echo.
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo ==========================================================
echo   SUCCESS! Your changes have been pushed to GitHub.
echo   Vercel will finish updating your live site in ~20s:
echo   --^> https://eatzy-virid.vercel.app/
echo ==========================================================
echo.
pause

