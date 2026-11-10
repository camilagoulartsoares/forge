"""Run repository checks from any working directory, stopping at the first failure."""

from pathlib import Path
import argparse
import shutil
import subprocess
import sys


ROOT = Path(__file__).resolve().parents[1]


def main(argv=None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--only", choices=["docs", "python", "php", "js"])
    selected = parser.parse_args(argv).only
    try:
        result = subprocess.run(
            ["git", "ls-files", "-z", "--cached", "--others", "--exclude-standard"],
            cwd=ROOT, capture_output=True, check=True,
        )
        files = sorted(set(result.stdout.decode("utf-8").strip("\0").split("\0")))
        php_files = [name for name in files if name.endswith(".php")] if selected in (None, "php") else []
        js_files = [name for name in files if name.endswith(".mjs")] if selected in (None, "js") else []
        js_tests = [name for name in js_files if name.endswith(".test.mjs")]
        for executable, needed in [("php", php_files), ("node", js_files)]:
            if needed and shutil.which(executable) is None:
                print(f"Required executable not found: {executable}", file=sys.stderr)
                return 2
        commands = []
        if selected in (None, "docs"):
            commands.append([sys.executable, "tools/check_docs.py"])
        if selected in (None, "python"):
            commands.append([sys.executable, "-m", "unittest", "discover", "-s", "tools", "-p", "test_*.py"])
        commands.extend(["php", "-l", name] for name in php_files)
        commands.extend(["php", name] for name in php_files if name.endswith("-test.php"))
        commands.extend(["node", "--check", name] for name in js_files)
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
    print(f"OK: {selected or 'all repository'} checks passed")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
