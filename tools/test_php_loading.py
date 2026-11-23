"""Public educational components must load independently in a fresh PHP process."""

from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]
EXAMPLES = ROOT / '04-arquitetura/arquitetura-hexagonal/exemplos'


@unittest.skipUnless(shutil.which('php'), 'PHP CLI required')
class PhpLoadingTests(unittest.TestCase):
    def test_each_order_component_resolves_its_own_dependencies_once(self):
        names = ['Pedido', 'ArmazenamentoPedidos', 'RegistrarPedido', 'PedidosEmMemoria', 'PedidosEmJson']
        code = "require_once $argv[1]; require_once $argv[1]; if (!class_exists($argv[2]) && !interface_exists($argv[2])) exit(1); echo 'loaded';"
        with tempfile.TemporaryDirectory() as directory:
            for name in names:
                with self.subTest(component=name):
                    result = subprocess.run(['php', '-d', 'display_errors=stderr', '-r', code,
                                             str(EXAMPLES / (name + '.php')), 'Forge\\Pedidos\\' + name],
                                            cwd=directory, capture_output=True, text=True, timeout=10)
                    self.assertEqual(0, result.returncode, result.stderr)
                    self.assertEqual('loaded', result.stdout)
                    self.assertEqual('', result.stderr)


if __name__ == '__main__':
    unittest.main()
