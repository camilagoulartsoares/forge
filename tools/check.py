"""Run repository checks from any working directory, stopping at the first failure."""

from pathlib import Path
import shutil
import subprocess
import sys


ROOT = Path(__file__).resolve().parents[1]


def main() -> int:
    try:
        result = subprocess.run(
            ["git", "ls-files", "-z", "--cached", "--others", "--exclude-standard"],
            cwd=ROOT, capture_output=True, check=True,
        )
        files = sorted(set(result.stdout.decode("utf-8").strip("\0").split("\0")))
        php_files = [name for name in files if name.endswith(".php")]
        js_tests = [name for name in files if name.endswith(".test.mjs")]
        for executable, needed in [("php", php_files), ("node", js_tests)]:
            if needed and shutil.which(executable) is None:
                print(f"Required executable not found: {executable}", file=sys.stderr)
                return 2
        commands = [
            [sys.executable, "tools/check_docs.py"],
            [sys.executable, "-m", "unittest", "discover", "-s", "tools", "-p", "test_*.py"],
            *[["php", "-l", name] for name in php_files],
            *[["php", name] for name in php_files if name.endswith("-test.php")],
        ]
        if js_tests:
            commands.append(["node", "--test", *js_tests])
        for command in commands:
            print("RUN: " + " ".join(command), flush=True)
            result = subprocess.run(command, cwd=ROOT)
            if result.returncode:
                return result.returncode if result.returncode > 0 else 1
    except (OSError, UnicodeError, subprocess.CalledProcessError) as error:
        print(f"Cannot run checks: {error}", file=sys.stderr)
        return 2
    print("OK: all repository checks passed")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
