@echo off
:: Usage: start.bat [project-name] [mode] [host-port]
:: mode: "wipe" to remove volumes then start in foreground (live logs), "attach" to run compose in foreground (live logs), or "nop" (default) to run detached
:: Examples:
::   start.bat                    -> uses default project name `chirag-backend` and port 9001
::   start.bat myproj             -> uses project name `myproj` and port 9001
::   start.bat myproj wipe        -> wipes volumes for `myproj` then starts
::   start.bat myproj wipe 8080   -> wipes then starts mapping host port 8080 -> container 80
@echo off
:: Single-command start script
:: This script WILL wipe the project's containers and volumes, prune unused volumes,
:: then build and start the compose stack in the foreground (attached) so you can see live logs.
::
:: IMPORTANT: This is destructive for this project's volumes. Do NOT run this if you want to preserve project data.

setlocal
:: Read project configuration from project-config.json (projectName, webPort)
for /f "usebackq delims=" %%A in (`powershell -NoProfile -Command "(Get-Content 'project-config.json' -Raw | ConvertFrom-Json).projectName"`) do set "COMPOSE_PROJECT_NAME=%%A"
if "%COMPOSE_PROJECT_NAME%"=="" set "COMPOSE_PROJECT_NAME=chirag-backend"
for /f "usebackq delims=" %%B in (`powershell -NoProfile -Command "(Get-Content 'project-config.json' -Raw | ConvertFrom-Json).webPort"`) do set "WEB_PORT=%%B"
if "%WEB_PORT%"=="" set "WEB_PORT=9001"

echo [WARN] This script will remove containers and volumes for project %COMPOSE_PROJECT_NAME% and start the stack attached.
echo [WARN] If you do NOT want to remove volumes, stop now (Ctrl+C). Waiting 3 seconds...
timeout /t 3 /nobreak >nul

echo [INFO] Stopping and removing old containers and volumes for project %COMPOSE_PROJECT_NAME%...
docker-compose down -v

echo [INFO] Pruning unused Docker volumes...
docker volume prune -f

echo [INFO] Starting with host port %WEB_PORT% -> container 80 (attached)
set "WEB_PORT=%WEB_PORT%"

docker-compose up --build

endlocal
	echo [INFO] Stopping and removing old containers and volumes for project %COMPOSE_PROJECT_NAME%...
	docker-compose down -v
	echo [INFO] Pruning unused Docker volumes...
	docker volume prune -f

	:: After wiping, default to attached mode so user sees logs immediately
	set "MODE=attach"

) else (
	echo [INFO] Stopping and removing old containers for project %COMPOSE_PROJECT_NAME% - volumes preserved...
	docker-compose down
)

echo [INFO] Starting with host port %WEB_PORT% -> container 80
set "WEB_PORT=%WEB_PORT%"
if "%MODE%"=="attach" (
	echo [INFO] Rebuilding and starting containers - foreground attached...
	docker-compose up --build
) else (
	echo [INFO] Rebuilding and starting containers - detached...
	docker-compose up --build -d
)

echo [INFO] To fully remove volumes and prune, re-run with the second arg: `start.bat %PROJ% wipe %WEB_PORT%`
