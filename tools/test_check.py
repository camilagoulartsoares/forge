"""Exercise failure propagation and executable discovery without running nested suites."""

import contextlib
import io
import subprocess
import unittest
from unittest.mock import patch

import check


class CheckRunnerTests(unittest.TestCase):
    def invoke(self, run, which=lambda name: name, argv=None):
        with patch("check.subprocess.run", side_effect=run), patch("check.shutil.which", side_effect=which):
            with contextlib.redirect_stdout(io.StringIO()), contextlib.redirect_stderr(io.StringIO()):
                return check.main(argv or [])

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

    def test_docs_only_does_not_require_php_or_node(self):
        calls = []

        def run(command, **kwargs):
            calls.append(command)
            return subprocess.CompletedProcess(command, 0, stdout=b"sample.php\0sample.test.mjs\0")

        self.assertEqual(0, self.invoke(run, which=lambda name: None, argv=["--only", "docs"]))
        self.assertEqual(2, len(calls))
        self.assertEqual("tools/check_docs.py", calls[1][1])

    def test_javascript_without_test_file_still_gets_syntax_checked(self):
        calls = []

        def run(command, **kwargs):
            calls.append(command)
            return subprocess.CompletedProcess(command, 0, stdout=b"source.mjs\0")

        self.assertEqual(0, self.invoke(run, argv=["--only", "js"]))
        self.assertIn(["node", "--check", "source.mjs"], calls)

    def test_timeout_stops_execution_and_returns_distinct_failure(self):
        calls = []

        def run(command, **kwargs):
            calls.append(command)
            self.assertEqual(0.1, kwargs["timeout"])
            if command[0] == "git":
                return subprocess.CompletedProcess(command, 0, stdout=b"")
            raise subprocess.TimeoutExpired(command, 0.1)

        self.assertEqual(124, self.invoke(run, argv=["--only", "docs", "--timeout", "0.1"]))
        self.assertEqual(2, len(calls))

    def test_timeout_must_be_positive_and_finite(self):
        for value in ["0", "-1", "nan", "inf", "wrong"]:
            with self.assertRaises(SystemExit):
                self.invoke(lambda *a, **k: None, argv=["--timeout", value])

    def test_git_diagnostic_includes_stderr_not_just_exit_status(self):
        error = subprocess.CalledProcessError(128, ["git"], stderr=b"fatal: cannot read index")
        with patch("check.subprocess.run", side_effect=error), contextlib.redirect_stderr(io.StringIO()) as output:
            self.assertEqual(2, check.main([]))
        self.assertIn("cannot read index", output.getvalue())
        self.assertNotIn("Traceback", output.getvalue())


if __name__ == "__main__":
    unittest.main()
