title: Run Tests and Auto‑Fix Errors
description: Run Playwright tests, collect console / test failures, ask Cascade to analyze and propose code fixes, then rerun.

steps:
  - name: Run Tests
    run: npx playwright test --reporter=json > test-results/output.json
  - name: Get Console Logs
    run: cat test-results/output.json
  - name: Analyze Failures
    cascade: |
      Here are the test results with failures and any console logs:
      ```json
      {{ output.json }}
      ```
      Please analyze the errors, identify likely causes, and propose code changes to fix them.
  - name: Apply Proposed Fixes
    cascade‑apply: yes
  - name: Rerun Tests
    run: npx playwright test
