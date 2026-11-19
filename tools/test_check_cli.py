"""Exercise the real entry point from a directory outside the repository."""

from pathlib import Path
import subprocess
import sys
import tempfile
import unittest

SCRIPT = Path(__file__).resolve().with_name("check.py")


class CheckCliTests(unittest.TestCase):
    def test_absolute_entry_point_resolves_repository_from_another_cwd(self):
        with tempfile.TemporaryDirectory() as directory:
            result = subprocess.run([sys.executable, str(SCRIPT), "--only", "docs", "--timeout", "10"],
                                    cwd=directory, capture_output=True, text=True, timeout=15)
        self.assertEqual(0, result.returncode, result.stderr)
        self.assertIn("Markdown documents", result.stdout)
        self.assertIn("OK: docs checks passed", result.stdout)
        self.assertNotIn("Traceback", result.stderr)


if __name__ == "__main__":
    unittest.main()
