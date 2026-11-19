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

    def test_backtick_in_info_prevents_opening_a_backtick_fence(self):
        errors = self.check("```php`invalid`\n[visible](absent.md)")
        self.assertEqual(1, len(errors))
        self.assertIn(":2: missing link", errors[0])
        self.assertEqual([], self.check("~~~php`allowed`\n[hidden](absent.md)\n~~~"))
        self.assertEqual([], self.check("```html <!-- literal info\n[hidden](absent.md)\n```"))

    def test_escaped_backticks_do_not_hide_links_but_backslashes_inside_code_are_literal(self):
        self.assertIn("missing link", self.check(r"\`[visible](absent.md)\`")[0])
        self.assertEqual([], self.check(r"\\`[hidden](absent.md)`"))
        errors = self.check(r"\```[hidden](absent.md)`` [visible](missing.md)")
        self.assertEqual(1, len(errors))
        self.assertIn("missing.md", errors[0])
        self.assertIn("missing link", self.check(r"`hidden \` [visible](absent.md)")[0])

    def test_escaped_quotes_in_link_titles_do_not_disable_validation(self):
        (self.root / "known.md").touch()
        errors = self.check(r'[known](known.md "A \"quoted\" title")' + "\n" +
                            r"[missing](absent.md 'It\'s missing')" + "\n" +
                            r'![image](absent.png "An escaped \\\\ slash")')
        self.assertEqual(2, len(errors))
        self.assertIn(":2: missing link", errors[0])
        self.assertIn(":3: missing link", errors[1])

    def test_only_spaces_and_tabs_may_follow_a_closing_fence(self):
        for suffix in [chr(0xA0), chr(0x2003)]:
            errors = self.check("```text\n```" + suffix + "\n[hidden](absent.md)\n```\n[visible](missing.md)")
            self.assertEqual(1, len(errors))
            self.assertIn(":5: missing link", errors[0])
        self.assertEqual([], self.check("```text\n[hidden](absent.md)\n``` \t"))

    def test_unicode_separators_are_content_not_markdown_line_endings(self):
        for separator in [chr(0x85), chr(0x2028), chr(0x2029)]:
            errors = self.check("Intro" + separator + "[missing](absent.md)")
            self.assertEqual(1, len(errors))
            self.assertIn(":1: missing link", errors[0])
        errors = self.check("```\n```" + chr(0x2028) + "[hidden](absent.md)")
        self.assertEqual(1, len(errors))
        self.assertIn("unclosed code fence", errors[0])

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

    def test_cli_skips_removed_documents_but_still_rejects_links_to_them(self):
        subprocess.run(["git", "init", "--quiet", str(self.root)], check=True)
        removed = self.root / "removed.md"
        removed.write_text("# Removed", encoding="utf-8")
        self.check("# Remaining")
        subprocess.run(["git", "-C", str(self.root), "add", "--", "README.md", "removed.md"], check=True)
        removed.unlink()
        command = [sys.executable, str(Path(__file__).with_name("check_docs.py")), "--root", str(self.root)]
        result = subprocess.run(command, capture_output=True, text=True)
        self.assertEqual(0, result.returncode, result.stderr)
        self.check("[removed](removed.md)")
        result = subprocess.run(command, capture_output=True, text=True)
        self.assertEqual(1, result.returncode)
        self.assertIn("missing link", result.stderr)

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

    def test_symlink_resolution_loop_is_reported_and_later_links_are_checked(self):
        original = Path.resolve

        def resolve(path, *args, **kwargs):
            if path.name == "loop.md":
                raise RuntimeError("Symlink loop")
            return original(path, *args, **kwargs)

        with patch.object(Path, "resolve", resolve):
            errors = self.check("[loop](loop.md)\n[missing](absent.md)")
        self.assertEqual(2, len(errors))
        self.assertIn(":1: invalid local path", errors[0])
        self.assertIn("Symlink loop", errors[0])
        self.assertIn(":2: missing link", errors[1])

    def test_invalid_percent_encoded_utf8_is_not_replaced_by_a_different_filename(self):
        (self.root / (chr(0xFFFD) + ".md")).touch()
        errors = self.check("[bad](%FF.md)\n[valid](%C3%A7.md)")
        self.assertIn(":1: invalid local path", errors[0])
        (self.root / (chr(0xE7) + ".md")).touch()
        self.assertEqual([], self.check("[valid](%C3%A7.md)"))

    def test_controls_in_html_entities_cannot_alias_an_existing_url(self):
        (self.root / "ab.md").touch()
        for target in ["a&#9;b.md", "a&#10;b.md", "a&#13;b.md"]:
            errors = self.check("[bad](<" + target + ">)")
            self.assertEqual(1, len(errors))
            self.assertIn("invalid link", errors[0])
            self.assertNotIn("\n", errors[0])

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
