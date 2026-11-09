"""Exercise failure propagation and executable discovery without running nested suites."""

import contextlib
import io
import subprocess
import unittest
from unittest.mock import patch

import check


class CheckRunnerTests(unittest.TestCase):
    def invoke(self, run, which=lambda name: name):
        with patch("check.subprocess.run", side_effect=run), patch("check.shutil.which", side_effect=which):
            with contextlib.redirect_stdout(io.StringIO()), contextlib.redirect_stderr(io.StringIO()):
                return check.main()

    def test_stops_at_first_failed_check(self):
        calls = []

        def run(command, **kwargs):
            calls.append(command)
            if command[0] == "git":
                return subprocess.CompletedProcess(command, 0, stdout=b"sample.php\0")
            return subprocess.CompletedProcess(command, 7)

        self.assertEqual(7, self.invoke(run))
        self.assertEqual(2, len(calls))

    def test_missing_runtime_is_not_success(self):
        def run(command, **kwargs):
            return subprocess.CompletedProcess(command, 0, stdout=b"sample.php\0")

        self.assertEqual(2, self.invoke(run, which=lambda name: None))

    def test_git_failure_is_operational_error(self):
        def run(command, **kwargs):
            raise subprocess.CalledProcessError(128, command)

        self.assertEqual(2, self.invoke(run))

    def test_runs_php_syntax_and_suites_and_js_tests(self):
        calls = []

        def run(command, **kwargs):
            calls.append(command)
            self.assertEqual(check.ROOT, kwargs["cwd"])
            return subprocess.CompletedProcess(command, 0, stdout=b"sample-test.php\0sample.test.mjs\0")

        self.assertEqual(0, self.invoke(run))
        self.assertIn(["php", "-l", "sample-test.php"], calls)
        self.assertIn(["php", "sample-test.php"], calls)
        self.assertIn(["node", "--test", "sample.test.mjs"], calls)


if __name__ == "__main__":
    unittest.main()
