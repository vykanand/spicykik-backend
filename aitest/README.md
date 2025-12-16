# AI Test Suite

This folder contains an automated testing and debugging setup using Playwright, integrated with AI for code fixing and feature completion.

## Setup

1. Copy this entire `aitest/` folder into your project root.
2. Run `npm install` in the `aitest/` directory to install dependencies.
3. Install Playwright browsers: `npx playwright install`.

## Usage

### Automated Debugging

- Run `npm run debug` to execute automated tests that detect console errors and page errors.
- Errors are logged to the terminal immediately.
- Tests fail if errors are detected, allowing AI to analyze logs and fix code.

### Debug Window

- Run `npm run debug:window` to open a maximized browser window for manual testing.
- Console errors, page errors, and navigation events are logged in real-time to the terminal.
- Screenshots are automatically taken on every page navigation, named with URL and timestamp (e.g., `screenshot_localhost_9001_index_php_2023-10-01T12-00-00-000Z.png`).
- Use screenshots for AI to analyze visual context and fix UI issues.

### Running Tests

- `npm test` runs all Playwright tests.
- `npm run test:debug` runs only the debug test.
- `npm run test:visual` runs the visual regression test to check UI layout and element positioning.

## AI Integration

- When errors are detected (via logs in terminal), describe them to the AI coding agent.
- The agent can analyze logs, identify issues, and suggest/fix code changes.
- For feature completion, write tests first, run them, and have AI implement the required code based on failures.

## Portability

This setup is self-contained. Copy the `aitest/` folder to any repository, run `npm install`, and start debugging/testing seamlessly.
