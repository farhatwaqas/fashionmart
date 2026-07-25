@echo off
title Fashion Corner Website
cd /d "%~dp0"

set "FASHION_NODE=node"
where node >nul 2>nul
if errorlevel 1 (
  set "FASHION_NODE=C:\Users\sheik\.cache\codex-runtimes\codex-primary-runtime\dependencies\node\bin\node.exe"
)

if not exist "%FASHION_NODE%" (
  where "%FASHION_NODE%" >nul 2>nul
  if errorlevel 1 (
    echo Node.js could not be found.
    echo Please install Node.js, then open this file again.
    pause
    exit /b 1
  )
)

echo.
echo Starting Fashion Corner...
echo Store: http://127.0.0.1:3000
echo Admin: http://127.0.0.1:3000/admin
echo.
start "" "http://127.0.0.1:3000/admin"
"%FASHION_NODE%" server.js
echo.
echo The website has stopped.
pause
