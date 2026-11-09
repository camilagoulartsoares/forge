import subprocess
import sys
import tempfile
from pathlib import Path
import unittest

from check_docs import check_file


class CheckDocsTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        self.document = self.root / "README.md"

    def check(self, text):
        self.document.write_text(text, encoding="utf-8")
        return check_file(self.document, self.root)

    def test_missing_local_link_has_line_number(self):
        self.assertIn(":2: missing link", self.check("# Study\n[missing](absent.md)")[0])

    def test_existing_link_with_fragment_and_encoded_space(self):
        (self.root / "my notes.md").touch()
        self.assertEqual([], self.check("[notes](my%20notes.md#topic)"))

    def test_angle_link_with_spaces(self):
        (self.root / "my notes.md").touch()
        self.assertEqual([], self.check("[notes](<my notes.md>)"))

    def test_external_links_and_anchors_need_no_network(self):
        self.assertEqual([], self.check("[web](https://example.invalid/x) [anchor](#topic)"))

    def test_nested_fences_and_inline_code_are_ignored(self):
        self.assertEqual([], self.check("````markdown\n```php\n[bad](absent.md)\n```\n````\n`[bad](absent.md)`"))

    def test_unclosed_fence(self):
        self.assertIn("unclosed code fence", self.check("~~~php\necho 1;")[0])

    def test_inline_code_requires_matching_delimiter_lengths(self):
        self.assertEqual([], self.check("Text ``code ` [not a link](absent.md)`` end"))
        self.assertIn("missing link", self.check("Text ``unmatched `[link](absent.md)")[0])

    def test_links_after_inline_code_are_still_checked(self):
        self.assertIn("missing link", self.check("`code` [real link](absent.md)")[0])

    def test_html_comments_are_ignored_without_shifting_lines(self):
        errors = self.check("<!--\n[hidden](absent.md)\n-->\n[visible](missing.md)")
        self.assertEqual(1, len(errors))
        self.assertIn(":4: missing link", errors[0])

    def test_unclosed_html_comment_hides_remaining_content(self):
        self.assertEqual([], self.check("# Title\n<!-- [draft](absent.md)\n```php"))

    def test_comment_markers_inside_code_are_literal(self):
        self.assertEqual([], self.check("```html\n<!-- literal\n```\nText `<!--` end"))

    def test_invalid_utf8(self):
        self.document.write_bytes(b"\xff")
        self.assertIn("cannot read UTF-8", check_file(self.document, self.root)[0])

    def test_link_outside_repository(self):
        self.assertIn("outside repository", self.check("[outside](../outside.md)")[0])

    def test_image_link_is_checked(self):
        self.assertIn("missing link", self.check("![diagram](missing.png)")[0])

    def test_malformed_url_reports_error_and_continues(self):
        errors = self.check("[invalid](https://[invalid)\n[missing](absent.md)")
        self.assertEqual(2, len(errors))
        self.assertIn(":1: invalid link", errors[0])
        self.assertIn(":2: missing link", errors[1])

    def test_cli_exit_code_and_git_ignored_files(self):
        subprocess.run(["git", "init", "--quiet", str(self.root)], check=True)
        (self.root / ".gitignore").write_text("ignored.md\n", encoding="utf-8")
        (self.root / "ignored.md").write_text("[bad](missing.md)", encoding="utf-8")
        self.check("# Valid")
        command = [sys.executable, str(Path(__file__).with_name("check_docs.py")), "--root", str(self.root)]
        self.assertEqual(0, subprocess.run(command, capture_output=True).returncode)
        self.check("[bad](missing.md)")
        self.assertEqual(1, subprocess.run(command, capture_output=True).returncode)


if __name__ == "__main__":
    unittest.main()
