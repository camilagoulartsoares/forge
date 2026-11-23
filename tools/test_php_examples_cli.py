"""Keep documented executable PHP examples independent of the current directory."""

from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]


@unittest.skipUnless(shutil.which('php'), 'PHP CLI required')
class PhpExampleCliTests(unittest.TestCase):
    def test_documented_examples_print_the_promised_results_from_another_directory(self):
        examples = [('01-fundamentos/php/introducao/exemplos/executar.php', 'Total: 60\n'),
                    ('01-fundamentos/php/poo/exemplos/executar.php', 'Saldo: 3\n')]
        with tempfile.TemporaryDirectory() as directory:
            for path, expected in examples:
                with self.subTest(example=path):
                    result = subprocess.run(['php', '-d', 'display_errors=stderr', str(ROOT / path)],
                                            cwd=directory, capture_output=True, text=True, timeout=10)
                    self.assertEqual(0, result.returncode, result.stderr)
                    self.assertEqual(expected, result.stdout)
                    self.assertEqual('', result.stderr)


if __name__ == '__main__':
    unittest.main()
