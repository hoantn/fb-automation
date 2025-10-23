@echo off
title FB Automation - Stop Project
echo =====================================
echo   Stopping FB Automation Services...
echo =====================================

taskkill /FI "WINDOWTITLE eq Laravel Server" /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq Queue Worker" /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq Realtime Server" /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq Ngrok" /F >nul 2>&1

echo ✅ All processes stopped.
pause
