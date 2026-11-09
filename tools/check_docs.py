"""Check UTF-8, fenced blocks and inline local Markdown links without dependencies."""

import argparse
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
    for number, line in enumerate(content.splitlines(), 1):
        match = FENCE.match(line)
        if match:
            marker, suffix = match.groups()
            if fence is None:
                fence = marker
            elif marker[0] == fence[0] and len(marker) >= len(fence) and not suffix.strip():
                fence = None
            continue
        if fence is not None:
            continue
        line = without_inline_code(line)
        for match in LINK.finditer(line):
            target = match[1].strip("<>")
            try:
                parsed = urlsplit(target)
            except ValueError as error:
                errors.append(f"{path}:{number}: invalid link: {target}: {error}")
                continue
            if parsed.scheme or parsed.netloc or not parsed.path:
                continue
            resolved = (path.parent / unquote(parsed.path)).resolve()
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
    result = subprocess.run(
        ["git", "-C", str(args.root), "ls-files", "-z", "--cached", "--others", "--exclude-standard", "--", "*.md"],
        capture_output=True,
        check=True,
    )
    paths = sorted(set(result.stdout.decode("utf-8").strip("\0").split("\0")) - {""})
    errors = [error for name in paths for error in check_file(args.root / name, args.root)]
    if errors:
        print("\n".join(errors), file=sys.stderr)
        return 1
    print(f"OK: {len(paths)} Markdown documents")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
