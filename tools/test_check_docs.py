import subprocess
import sys
import tempfile
from pathlib import Path
import unittest
from unittest.mock import patch
import io

from check_docs import check_file, main, path_case_matches, without_inline_code


class CheckDocsTests(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.addCleanup(self.temp.cleanup)
        self.root = Path(self.temp.name)
        self.document = self.root / "README.md"

    def check(self, text):
        self.document.write_text(text, encoding="utf-8")
        return check_file(self.document, self.root)

    def test_repeated_links_share_directory_scan_but_new_checks_refresh_cache(self):
        (self.root / "target.md").touch()
        original = Path.iterdir
        scanned = []

        def inspect(path):
            scanned.append(path)
            return original(path)

        with patch.object(Path, "iterdir", inspect):
            self.assertEqual([], self.check("[one](target.md) [two](target.md)"))
        self.assertEqual([self.root], scanned)
        (self.root / "new.md").touch()
        self.assertEqual([], self.check("[new](new.md)"))
        self.assertFalse(path_case_matches(self.root / "TARGET.md", self.root))

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

    def test_cli_non_repository_returns_diagnostic_without_traceback(self):
        command = [sys.executable, str(Path(__file__).with_name("check_docs.py")), "--root", str(self.root)]
        result = subprocess.run(command, capture_output=True, text=True)
        self.assertEqual(2, result.returncode)
        self.assertIn("Cannot enumerate documents", result.stderr)
        self.assertNotIn("Traceback", result.stderr)

    def test_missing_git_returns_operational_error(self):
        with patch("sys.argv", ["check_docs.py"]), patch("sys.stderr", new_callable=io.StringIO) as errors:
            with patch("check_docs.subprocess.run", side_effect=FileNotFoundError("git unavailable")):
                self.assertEqual(2, main())
        self.assertIn("git unavailable", errors.getvalue())

    def test_invalid_local_path_does_not_hide_later_errors(self):
        errors = self.check("[bad](file%00.md)\n[missing](absent.md)")
        self.assertEqual(2, len(errors))
        self.assertIn(":1: invalid local path", errors[0])
        self.assertIn(":2: missing link", errors[1])

    def test_escaped_opening_bracket_is_literal_but_double_escape_is_not(self):
        self.assertEqual([], self.check(r"\[literal](missing.md) !\[literal](missing.png)"))
        self.assertIn("missing link", self.check(r"\\[real](missing.md)")[0])
        self.assertIn("missing link", self.check(r"\![real](missing.md)")[0])

    def test_local_link_decodes_html_character_references(self):
        (self.root / "a&b.md").touch()
        self.assertEqual([], self.check("[notes](a&amp;b.md) [notes](a&#38;b.md)"))
        self.assertIn("missing link", self.check("[notes](missing&amp;file.md)")[0])

    def test_local_paths_require_exact_case_on_every_platform(self):
        folder = self.root / 'Guides'
        folder.mkdir()
        (folder / 'Notes.md').touch()
        self.assertTrue(path_case_matches(folder / 'Notes.md', self.root))
        self.assertFalse(path_case_matches(folder / 'notes.md', self.root))
        self.assertFalse(path_case_matches(self.root / 'guides/Notes.md', self.root))
        self.assertEqual([], self.check('[notes](Guides/Notes.md)'))
        self.assertTrue(self.check('[notes](Guides/notes.md)'))

    def test_links_with_single_quoted_titles_are_checked(self):
        (self.root / 'known.md').touch()
        errors = self.check("[known](known.md 'Description')\n[missing](absent.md 'Description')\n![image](absent.png \"Image\")")
        self.assertEqual(2, len(errors))
        self.assertIn(':2: missing link', errors[0])
        self.assertIn(':3: missing link', errors[1])

    def test_many_unmatched_backtick_runs_remain_literal(self):
        text = 'x'.join('`' * length for length in range(1, 1200))
        self.assertEqual(text, without_inline_code(text))
        self.assertEqual('after', without_inline_code('``a`b``after'))


if __name__ == "__main__":
    unittest.main()
