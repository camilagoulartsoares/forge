"""Run repository checks from any working directory, stopping at the first failure."""

from pathlib import Path
import argparse
import math
import shutil
import subprocess
import sys


ROOT = Path(__file__).resolve().parents[1]


def positive_timeout(raw):
    try:
        value = float(raw)
    except ValueError:
        raise argparse.ArgumentTypeError("timeout must be a positive finite number") from None
    if not math.isfinite(value) or value <= 0:
        raise argparse.ArgumentTypeError("timeout must be a positive finite number")
    return value


def main(argv=None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--only", choices=["docs", "python", "php", "js"])
    parser.add_argument("--timeout", type=positive_timeout, help="maximum seconds per command")
    args = parser.parse_args(argv)
    selected = args.only
    try:
        result = subprocess.run(
            ["git", "ls-files", "-z", "--cached", "--others", "--exclude-standard"],
            cwd=ROOT, capture_output=True, check=True, timeout=args.timeout,
        )
        files = sorted(set(result.stdout.decode("utf-8").strip("\0").split("\0")))
        php_files = ["./" + name for name in files if name.endswith(".php")] if selected in (None, "php") else []
        js_files = ["./" + name for name in files if name.endswith(".mjs")] if selected in (None, "js") else []
        js_tests = [name for name in js_files if name.endswith(".test.mjs")]
        python_files = ["./" + name for name in files if name.endswith(".py")] if selected in (None, "python") else []
        json_files = ["./" + name for name in files if name.endswith(".json")] if selected in (None, "python") else []
        executables = {}
        for executable, needed in [("php", php_files), ("node", js_files)]:
            if needed:
                resolved = shutil.which(executable)
                if resolved is None:
                    print(f"Required executable not found: {executable}", file=sys.stderr)
                    return 2
                executables[executable] = resolved
        commands = []
        if selected is None:
            commands.extend([["git", "diff", "--check"], ["git", "diff", "--cached", "--check"]])
        if selected in (None, "docs"):
            commands.append([sys.executable, "tools/check_docs.py"])
        if selected in (None, "python"):
            if python_files:
                commands.append([sys.executable, "-m", "py_compile", *python_files])
            if json_files:
                commands.append([sys.executable, "tools/check_json.py", *json_files])
            commands.append([sys.executable, "-m", "unittest", "discover", "-s", "tools", "-p", "test_*.py"])
        commands.extend(["php", "-l", name] for name in php_files)
        commands.extend(["php", name] for name in php_files if name.endswith("-test.php"))
        commands.extend(["node", "--check", name] for name in js_files)
        if js_tests:
            commands.append(["node", "--test", *js_tests])
        for command in commands:
            if command[0] in executables:
                command = [executables[command[0]], *command[1:]]
            print("RUN: " + " ".join(command), flush=True)
            result = subprocess.run(command, cwd=ROOT, timeout=args.timeout)
            if result.returncode:
                return result.returncode if result.returncode > 0 else 1
    except KeyboardInterrupt:
        print("Checks interrupted.", file=sys.stderr)
        return 130
    except subprocess.TimeoutExpired as error:
        print(f"Check timed out after {error.timeout} seconds: {error.cmd}", file=sys.stderr)
        return 124
    except subprocess.CalledProcessError as error:
        detail = (error.stderr or b"").decode("utf-8", errors="replace").strip()
        print(f"Cannot enumerate repository files: {detail or error}", file=sys.stderr)
        return 2
    except (OSError, UnicodeError) as error:
        print(f"Cannot run checks: {error}", file=sys.stderr)
        return 2
    print(f"OK: {selected or 'all repository'} checks passed")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
