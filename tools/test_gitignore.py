"""Check ignore rules in an isolated repository without creating real secrets."""

from pathlib import Path
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]


class IgnoreRulesTests(unittest.TestCase):
    def test_environment_variants_are_ignored_but_templates_remain_visible(self):
        with tempfile.TemporaryDirectory() as directory:
            root = Path(directory)
            subprocess.run(["git", "init", "--quiet", str(root)], check=True)
            (root / ".gitignore").write_bytes((ROOT / ".gitignore").read_bytes())
            for name in [".env", ".env.production", "app/.env.test", "app/.env.local"]:
                result = subprocess.run(["git", "-C", str(root), "check-ignore", "--quiet", "--no-index", "--", name])
                self.assertEqual(0, result.returncode, name)
            for name in [".env.example", "app/.env.test.example"]:
                result = subprocess.run(["git", "-C", str(root), "check-ignore", "--quiet", "--no-index", "--", name])
                self.assertEqual(1, result.returncode, name)


if __name__ == "__main__":
    unittest.main()
