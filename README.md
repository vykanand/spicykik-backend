# billionerp

No code low code erp made with angularjs and php

For local testing - `./start.bat` (windows 10/11)
start automated browser testing (Playwright) - `npm run debug:window`

Run with proper permissions locally for realtime development changes
docker run -v $(pwd):/var/www/html -p 9001:80 --user $(id -u):$(id -g) billion1

to shutdown use docker-compose down

Automated debugging and code fixing with Playwright and the AI coding agent as follows:

The app server is running via docker-compose.
Created a Playwright test (tests/debug.spec.js) that captures browser console logs and errors during test runs.
Developed an automation script (automate-debug.js) that runs the debug test, collects logs, detects errors, and outputs them for AI analysis.
Updated package.json with a debug script to run the automation script easily.
