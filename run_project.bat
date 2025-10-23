@echo off
title FB Automation - Run Project
echo =====================================
echo   Starting FB Automation System...
echo =====================================

cd /d D:\xampp\htdocs\fb-automation

echo [1/4] Starting Laravel Server...
start "Laravel Server" cmd /k "D:\xampp\php\php.exe artisan serve"

timeout /t 3 >nul
echo [2/4] Starting Laravel Queue Worker...
start "Queue Worker" cmd /k "D:\xampp\php\php.exe artisan queue:work --queue=fb-send,fb-webhook,broadcast"

timeout /t 3 >nul
echo [3/4] Starting Ngrok Tunnel...
start "Ngrok" cmd /k ngrok http 8000

timeout /t 2 >nul
start http://localhost:8000/admin/login

echo.
echo ✅ All services started successfully!
pause
