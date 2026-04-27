import { execFileSync } from 'node:child_process';

const runInApp = (script: string): void => {
  execFileSync('docker', ['compose', 'exec', '-T', 'app', 'php', script], {
    stdio: 'inherit',
  });
};

export default function globalSetup(): void {
  runInApp('database/migrate.php');
  runInApp('database/seed-e2e.php');
}
