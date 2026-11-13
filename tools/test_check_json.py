import contextlib
import io
import tempfile
from pathlib import Path
import unittest
import subprocess
import sys
import os

from check_json import main, validate


class CheckJsonTests(unittest.TestCase):
    def setUp(self):
        temp = tempfile.TemporaryDirectory()
        self.addCleanup(temp.cleanup)
        self.file = Path(temp.name) / "fixture.json"

    def write(self, value):
        self.file.write_text(value, encoding="utf-8")
        return self.file

    def test_valid_nested_fixture(self):
        self.assertEqual({"items": [1, None, True]}, validate(self.write('{"items":[1,null,true]}')))

    def test_ambiguous_nested_keys_are_rejected(self):
        with self.assertRaisesRegex(ValueError, "duplicate key"):
            validate(self.write('{"item":{"id":1,"id":2}}'))

    def test_nonstandard_numbers_are_rejected(self):
        for value in ["NaN", "Infinity", "-Infinity"]:
            with self.assertRaisesRegex(ValueError, "non-standard"):
                validate(self.write(value))

    def test_bad_utf8_and_syntax_are_failures(self):
        self.file.write_bytes(b'"\xff"')
        with self.assertRaises(ValueError):
            validate(self.file)
        with self.assertRaises(ValueError):
            validate(self.write('{'))

    def test_cli_reports_error_instead_of_traceback(self):
        with contextlib.redirect_stderr(io.StringIO()) as output:
            self.assertEqual(1, main([str(self.write('{'))]))
        self.assertIn(str(self.file), output.getvalue())
        self.assertNotIn("Traceback", output.getvalue())

    def test_cli_continues_after_excessive_nesting_without_traceback(self):
        self.write('[' * 2000 + '0' + ']' * 2000)
        other = self.file.with_name('other.json')
        other.write_text('{', encoding='utf-8')
        result = subprocess.run([sys.executable, str(Path(__file__).with_name('check_json.py')),
                                 str(self.file), str(other)], capture_output=True, text=True)
        self.assertEqual(1, result.returncode)
        self.assertIn(str(self.file), result.stderr)
        self.assertIn(str(other), result.stderr)
        self.assertNotIn('Traceback', result.stderr)

    def test_duplicate_unicode_key_has_safe_ascii_diagnostic(self):
        self.write(r'{"\ud83d\ude00":1,"\ud83d\ude00":2}')
        result = subprocess.run([sys.executable, str(Path(__file__).with_name('check_json.py')), str(self.file)],
                                capture_output=True, text=True,
                                env={**os.environ, 'PYTHONIOENCODING': 'ascii'})
        self.assertEqual(1, result.returncode)
        self.assertIn('duplicate key', result.stderr)
        self.assertNotIn('Traceback', result.stderr)


if __name__ == "__main__":
    unittest.main()
