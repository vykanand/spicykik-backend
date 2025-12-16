const { exec } = require('child_process');

function runTests() {
  console.log('Running Playwright tests for debugging...');
  exec('npx playwright test tests/debug.spec.js', (error, stdout, stderr) => {
    if (stdout) {
      console.log(stdout);
    }
    if (stderr) {
      console.error(stderr);
    }
    if (error) {
      console.error('Test run failed:', error.message);
    }
  });
}

// Run tests
runTests();
