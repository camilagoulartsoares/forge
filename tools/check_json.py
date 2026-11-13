"""Validate UTF-8 JSON fixtures, rejecting ambiguous keys and non-standard numbers."""

import argparse
import json
from pathlib import Path
import sys


def unique_keys(pairs):
    result = {}
    for key, value in pairs:
        if key in result:
            raise ValueError(f"duplicate key: {ascii(key)}")
        result[key] = value
    return result


def reject_constant(value):
    raise ValueError(f"non-standard number: {value}")


def validate(path):
    return json.loads(Path(path).read_text(encoding="utf-8"),
                      object_pairs_hook=unique_keys, parse_constant=reject_constant)


def main(argv=None):
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("paths", nargs="+")
    args = parser.parse_args(argv)
    errors = []
    for name in args.paths:
        try:
            validate(name)
        except (OSError, ValueError, RecursionError) as error:
            errors.append(f"{name}: {error}")
    if errors:
        print("\n".join(errors), file=sys.stderr)
        return 1
    print(f"OK: {len(args.paths)} JSON files")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
