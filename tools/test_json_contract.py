"""Shared acceptance contract for the three strict JSON readers."""

import json
from pathlib import Path
import tempfile
import unittest

from check_json import validate

CASES = Path(__file__).resolve().parents[1] / "01-fundamentos/php/testes-automatizados/exemplos/casos-json.json"


class JsonContractTests(unittest.TestCase):
    def test_same_acceptance_cases_as_php_and_javascript(self):
        cases = json.loads(CASES.read_text(encoding="utf-8"))
        with tempfile.TemporaryDirectory() as directory:
            fixture = Path(directory) / "input.json"
            for case in cases:
                with self.subTest(name=case["nome"]):
                    fixture.write_text(case["texto"], encoding="utf-8")
                    if case["valido"]:
                        validate(fixture)
                    else:
                        with self.assertRaises(ValueError):
                            validate(fixture)


if __name__ == "__main__":
    unittest.main()
