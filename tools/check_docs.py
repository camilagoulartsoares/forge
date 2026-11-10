"""Check UTF-8, fenced blocks and inline local Markdown links without dependencies."""

import argparse
from html import unescape
from pathlib import Path
import re
import subprocess
import sys
from urllib.parse import unquote, urlsplit


LINK = re.compile(r'!?\[[^\]\n]*\]\((<[^>\n]+>|[^\s)]+)(?:\s+"[^"]*")?\)')
FENCE = re.compile(r"^ {0,3}(`{3,}|~{3,})(.*)$")


def without_inline_code(line: str) -> str:
    """Only matching backtick runs delimit code; unmatched runs remain literal."""
    runs = list(re.finditer(r"`+", line))
    cursor = 0
    pieces = []
    index = 0
    while index < len(runs):
        opening = runs[index]
        closing = next((j for j in range(index + 1, len(runs))
                        if len(runs[j][0]) == len(opening[0])), None)
        if closing is None:
            index += 1
            continue
        pieces.append(line[cursor:opening.start()])
        cursor = runs[closing].end()
        index = closing + 1
    pieces.append(line[cursor:])
    return "".join(pieces)


def check_file(path: Path, root: Path) -> list[str]:
    errors = []
    try:
        content = path.read_text(encoding="utf-8")
    except (OSError, UnicodeError) as error:
        return [f"{path}: cannot read UTF-8: {error}"]
    fence = None
    in_comment = False
    for number, line in enumerate(content.splitlines(), 1):
        if fence is not None:
            match = FENCE.match(line)
            if match and match[1][0] == fence[0] and len(match[1]) >= len(fence) and not match[2].strip():
                fence = None
            continue
        line = without_inline_code(line)
        visible = []
        while line:
            if in_comment:
                end = line.find("-->")
                if end == -1:
                    break
                line = line[end + 3:]
                in_comment = False
            else:
                start = line.find("<!--")
                if start == -1:
                    visible.append(line)
                    break
                visible.append(line[:start])
                line = line[start + 4:]
                in_comment = True
        line = "".join(visible)
        match = FENCE.match(line)
        if match:
            fence = match[1]
            continue
        for match in LINK.finditer(line):
            bracket = match.start() + int(match[0].startswith("!"))
            prefix = line[:bracket]
            if (len(prefix) - len(prefix.rstrip("\\"))) % 2:
                continue
            target = unescape(match[1].strip("<>"))
            try:
                parsed = urlsplit(target)
            except ValueError as error:
                errors.append(f"{path}:{number}: invalid link: {target}: {error}")
                continue
            if parsed.scheme or parsed.netloc or not parsed.path:
                continue
            try:
                decoded = unquote(parsed.path)
                if "\0" in decoded:
                    raise ValueError("NUL character in path")
                resolved = (path.parent / decoded).resolve()
            except (ValueError, OSError) as error:
                errors.append(f"{path}:{number}: invalid local path: {target}: {error}")
                continue
            if not resolved.is_relative_to(root.resolve()):
                errors.append(f"{path}:{number}: link outside repository: {target}")
            elif not resolved.exists():
                errors.append(f"{path}:{number}: missing link: {target}")
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
        paths = sorted(set(result.stdout.decode("utf-8").strip("\0").split("\0")) - {""})
    except (OSError, subprocess.CalledProcessError, UnicodeError) as error:
        detail = error.stderr.decode("utf-8", errors="replace").strip() if isinstance(error, subprocess.CalledProcessError) else str(error)
        print(f"Cannot enumerate documents: {detail}", file=sys.stderr)
        return 2
    errors = [error for name in paths for error in check_file(args.root / name, args.root)]
    if errors:
        print("\n".join(errors), file=sys.stderr)
        return 1
    print(f"OK: {len(paths)} Markdown documents")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
