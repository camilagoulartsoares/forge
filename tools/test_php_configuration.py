"""Verify test helpers still fail with native assertions compiled out."""

from pathlib import Path
import shutil
import subprocess
import unittest

PHP = shutil.which("php")
HELPERS = Path(__file__).resolve().parents[1] / "01-fundamentos/php/testes-automatizados/exemplos/verificacoes.php"


@unittest.skipUnless(PHP, "PHP CLI is required")
class PhpConfigurationTests(unittest.TestCase):
    def test_explicit_helpers_do_not_depend_on_zend_assertions(self):
        cases = [("verificarIgual(1, 1);", True),
                 ("verificarIgual(1, '1');", False),
                 ("verificarExcecao(RuntimeException::class, function () {});", False)]
        for action, success in cases:
            with self.subTest(action=action):
                code = "assert(false); require $argv[1]; " + action
                result = subprocess.run([PHP, "-d", "zend.assertions=-1", "-r", code, str(HELPERS)],
                                        capture_output=True, timeout=5)
                self.assertEqual(success, result.returncode == 0)
                if not success:
                    self.assertIn(b"RuntimeException", result.stdout + result.stderr)


if __name__ == "__main__":
    unittest.main()
