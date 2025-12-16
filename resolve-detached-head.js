const { execSync } = require('child_process');

function runGitCommand(command) {
  try {
    const result = execSync(command, { stdio: 'pipe' }).toString();
    return result;
  } catch (error) {
    console.error('Error running command:', command);
    console.error(error.message);
    process.exit(1);
  }
}

function isDetachedHEAD() {
  const status = runGitCommand('git status');
  return status.includes('HEAD detached');
}

function fixDetachedHead() {
  console.log('Checking if we are in a detached HEAD state...');

  if (isDetachedHEAD()) {
    console.log('Detached HEAD detected. Attempting to fix...');

    // Check if main exists locally
    const branches = runGitCommand('git branch').split('\n').map(branch => branch.trim());
    if (!branches.includes('main')) {
      console.log('Local branch "main" does not exist. Creating it...');
      runGitCommand('git checkout -b main origin/main');
    } else {
      console.log('Switching to the "main" branch...');
      runGitCommand('git checkout main');
    }

    // Ensure "main" is tracking "origin/main"
    const upstream = runGitCommand('git branch -vv');
    if (!upstream.includes('origin/main')) {
      console.log('Setting upstream of "main" to "origin/main"...');
      runGitCommand('git branch --set-upstream-to=origin/main main');
    }

    // Pull the latest changes from origin/main
    console.log('Pulling the latest changes from "origin/main"...');
    runGitCommand('git pull origin main');

    console.log('HEAD has been reattached to the "main" branch.');
  } else {
    console.log('No detached HEAD detected. You are on a branch.');
  }
}

fixDetachedHead();

