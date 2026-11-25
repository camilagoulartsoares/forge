"""Check UTF-8, fenced blocks and inline local Markdown links without dependencies."""

import argparse
from html.entities import html5
from pathlib import Path
import os
import re
import subprocess
import sys
from urllib.parse import unquote, urlsplit


DESTINATION = re.compile(r"""\((<[^>\n]+>|[^\s)]+)(?:\s+(?:"(?:[^"\\\n]|\\.)*"|'(?:[^'\\\n]|\\.)*'))?\)""")
FENCE = re.compile(r"^ {0,3}(`{3,}|~{3,})(.*)$")


def link_destinations(line):
    """Recognize balanced labels without retrying a regex at every opening bracket."""
    openings = []
    position = 0
    while position < len(line):
        char = line[position]
        if char == "\\":
            position += 2
            continue
        if char == '[':
            escape_cursor = position - 2
            while escape_cursor >= 0 and line[escape_cursor] == chr(92):
                escape_cursor -= 1
            image = position > 0 and line[position - 1] == '!' and (position - 2 - escape_cursor) % 2 == 0
            openings.append(image)
        elif char == ']' and openings:
            image = openings.pop()
            destination = DESTINATION.match(line, position + 1)
            if destination:
                yield destination[1]
                position = destination.end()
                if not image:
                    openings.clear()  # Links may contain images, but not other links.
                continue
        position += 1


def decode_destination(target):
    # CommonMark escapes punctuation; character references require a semicolon.
    # One pass keeps an escaped ampersand from becoming a new character reference.
    def replace_reference(match):
        if match[1] is not None:
            return match[1]
        reference = match[0]
        if reference.startswith('&#'):
            digits = reference[2:-1]
            codepoint = int(digits[1:], 16) if digits.startswith(('x', 'X')) else int(digits)
            if codepoint == 0 or 0xD800 <= codepoint <= 0xDFFF or codepoint > 0x10FFFF:
                return chr(0xFFFD)
            return chr(codepoint)
        return html5.get(reference[1:], reference)

    return re.sub(r'\\([!-/:-@\[-`{-~])|&(?:\#[xX][0-9a-fA-F]{1,6}|\#[0-9]{1,7}|[A-Za-z][A-Za-z0-9]+);',
                  replace_reference, target)



def path_case_matches(candidate: Path, root: Path, cache: dict | None = None) -> bool:
    # abspath normalizes dots but preserves the spelling erased by Windows resolve().
    candidate = Path(os.path.abspath(candidate))
    current = Path(os.path.abspath(root))
    try:
        parts = candidate.relative_to(current).parts
    except ValueError:
        return False
    cache = {} if cache is None else cache
    for part in parts:
        if current not in cache:
            cache[current] = {entry.name for entry in current.iterdir()}
        if part not in cache[current]:
            return False
        current = current / part
    return True


def visible_inline(line: str, in_comment=False, *, comments=True, code_gap=""):
    """Process code and comments in source order; delimiters inside either are literal."""
    tokens = list(re.finditer(r"<!--|-->|`+" if comments else r"`+", line))
    following = {}
    escaped = {}
    last_by_length = {}
    for index in range(len(tokens) - 1, -1, -1):
        token = tokens[index]
        if not token[0].startswith('`'):
            continue
        cursor = token.start() - 1
        while cursor >= 0 and line[cursor] == chr(92):
            cursor -= 1
        escaped[index] = (token.start() - 1 - cursor) % 2
        length = len(token[0])
        following[index] = last_by_length.get(length - escaped[index])
        last_by_length[length] = index
    cursor, index = 0, 0
    pieces = []
    while index < len(tokens):
        token = tokens[index]
        if in_comment:
            if token[0] == '-->':
                in_comment = False
                cursor = token.end()
        elif token[0] == '<!--':
            escape_cursor = token.start() - 1
            while escape_cursor >= 0 and line[escape_cursor] == chr(92):
                escape_cursor -= 1
            if (token.start() - 1 - escape_cursor) % 2 == 0:
                pieces.append(line[cursor:token.start()] + ' ')
                cursor = token.end()
                in_comment = True
        elif token[0].startswith('`'):
            closing = following[index]
            if closing is not None:
                pieces.append(line[cursor:token.start() + escaped[index]] + code_gap)
                cursor = tokens[closing].end()
                index = closing
        index += 1
    if not in_comment:
        pieces.append(line[cursor:])
    return ''.join(pieces), in_comment


def without_inline_code(line: str) -> str:
    return visible_inline(line, comments=False)[0]


def check_file(path: Path, root: Path, case_cache: dict | None = None) -> list[str]:
    errors = []
    case_cache = {} if case_cache is None else case_cache
    try:
        content = path.read_text(encoding="utf-8")
    except (OSError, UnicodeError) as error:
        return [f"{path}: cannot read UTF-8: {error}"]
    fence = None
    in_comment = False
    for number, line in enumerate(content.split("\n"), 1):
        if fence is not None:
            match = FENCE.match(line)
            if match and match[1][0] == fence[0] and len(match[1]) >= len(fence) and not match[2].strip(" \t"):
                fence = None
            continue
        opening = FENCE.match(line) if not in_comment else None
        if opening and (opening[1][0] != "`" or "`" not in opening[2]):
            fence = opening[1]
            continue
        line, in_comment = visible_inline(line, in_comment, code_gap=" ")
        for destination in link_destinations(line):
            target = decode_destination(destination.strip("<>"))
            try:
                if any(ord(char) < 32 or ord(char) == 127 for char in target):
                    raise ValueError("Control character in URL")
                parsed = urlsplit(target)
            except ValueError as error:
                errors.append(f"{path}:{number}: invalid link: {ascii(target)}: {error}")
                continue
            if parsed.scheme.lower() == 'file' or (len(parsed.scheme) == 1
                    and unquote(parsed.path).startswith(('/', chr(92)))):
                errors.append(f"{path}:{number}: nonportable local URL: {ascii(target)}")
                continue
            if parsed.scheme or parsed.netloc or not parsed.path:
                continue
            try:
                decoded = unquote(parsed.path, errors="strict")
                if "\\" in decoded:
                    raise ValueError("Use forward slashes in Markdown paths")
                if "\0" in decoded:
                    raise ValueError("NUL character in path")
                resolved = (path.parent / decoded).resolve()
            except (ValueError, OSError, RuntimeError) as error:
                errors.append(f"{path}:{number}: invalid local path: {target}: {error}")
                continue
            if not resolved.is_relative_to(root.resolve()):
                errors.append(f"{path}:{number}: link outside repository: {target}")
            elif not resolved.exists():
                errors.append(f"{path}:{number}: missing link: {target}")
            else:
                try:
                    if not path_case_matches(path.parent / decoded, root, case_cache):
                        errors.append(f"{path}:{number}: incorrect path case: {target}")
                except OSError as error:
                    errors.append(f"{path}:{number}: cannot inspect local path: {target}: {error}")
    if fence is not None:
        errors.append(f"{path}: unclosed code fence")
    return errors


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", type=Path, default=Path(__file__).resolve().parents[1])
    args = parser.parse_args()
    try:
        result = subprocess.run(
            ["git", "-C", str(args.root), "ls-files", "-z", "--cached", "--others", "--exclude-standard", "--", "*.md"],
            capture_output=True,
            check=True,
        )
        paths = sorted({name for name in result.stdout.decode("utf-8").split("\0")
                        if name and (args.root / name).is_file()})
    except (OSError, subprocess.CalledProcessError, UnicodeError) as error:
        detail = error.stderr.decode("utf-8", errors="replace").strip() if isinstance(error, subprocess.CalledProcessError) else str(error)
        print(f"Cannot enumerate documents: {detail}", file=sys.stderr)
        return 2
    case_cache = {}
    errors = [error for name in paths for error in check_file(args.root / name, args.root, case_cache)]
    if errors:
        print("\n".join(errors), file=sys.stderr)
        return 1
    print(f"OK: {len(paths)} Markdown documents")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
