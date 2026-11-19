"""Cross-process integrity checks for the educational local JSON adapter."""

import json
from pathlib import Path
import shutil
import subprocess
import tempfile
import time
import unittest


PHP = shutil.which("php")
ADAPTER = Path(__file__).resolve().parents[1] / "04-arquitetura/arquitetura-hexagonal/exemplos/PedidosEmJson.php"
WORKER = r"""
require $argv[1];
$repo = new Forge\Pedidos\PedidosEmJson($argv[2]);
if ($repo->existe($argv[3])) exit(4);
file_put_contents($argv[4], 'ready');
$deadline = hrtime(true) + 5000000000;
while (!file_exists($argv[5])) {
    if (hrtime(true) > $deadline) exit(5);
    usleep(1000);
}
try {
    $repo->salvar(new Forge\Pedidos\Pedido($argv[3], [['codigo' => 'Livro', 'quantidade' => 1]]));
    exit(0);
} catch (DomainException $error) {
    exit(3);
}
"""


@unittest.skipUnless(PHP, "PHP CLI is required for cross-process adapter tests")
class PedidosConcurrencyTests(unittest.TestCase):
    def race(self, identifiers):
        with tempfile.TemporaryDirectory(prefix="forge-race-") as directory:
            root = Path(directory)
            database = root / "pedidos.json"
            gate = root / "go"
            processes = []
            try:
                for index, identifier in enumerate(identifiers):
                    processes.append(subprocess.Popen(
                        [PHP, "-r", WORKER, str(ADAPTER), str(database), identifier,
                         str(root / f"ready-{index}"), str(gate)],
                        stdout=subprocess.PIPE, stderr=subprocess.PIPE,
                    ))
                deadline = time.monotonic() + 5
                while not all((root / f"ready-{index}").exists() for index in range(2)):
                    self.assertTrue(all(process.poll() is None for process in processes), "Worker exited before barrier")
                    self.assertLess(time.monotonic(), deadline, "Workers did not reach barrier")
                    time.sleep(0.005)
                gate.write_text("go", encoding="ascii")
                codes = []
                for process in processes:
                    stdout, stderr = process.communicate(timeout=10)
                    self.assertEqual(b"", stdout)
                    self.assertEqual(b"", stderr)
                    codes.append(process.returncode)
                return sorted(codes), json.loads(database.read_text(encoding="utf-8"))
            finally:
                for process in processes:
                    if process.poll() is None:
                        process.kill()
                    process.communicate(timeout=10)

    def test_reader_sees_intact_original_while_writer_appends_orders(self):
        reader_code = r"""
require $argv[1];
$repo = new Forge\Pedidos\PedidosEmJson($argv[2]);
$deadline = hrtime(true) + 8000000000;
$reads = 0;
do {
    $original = $repo->buscar('original');
    if ($original === null || $original->itens()[0]['quantidade'] !== 1) exit(6);
    if ($reads++ === 0) file_put_contents($argv[3], 'ready');
    usleep(1000);
} while (!file_exists($argv[4]) && hrtime(true) < $deadline);
if (!file_exists($argv[4])) exit(7);
echo $reads;
"""
        writer_code = r"""
require $argv[1];
$repo = new Forge\Pedidos\PedidosEmJson($argv[2]);
for ($i = 1; $i <= 20; $i++) {
    $repo->salvar(new Forge\Pedidos\Pedido('id-' . $i, [['codigo' => 'Livro', 'quantidade' => 1]]));
    usleep(1000);
}
"""
        with tempfile.TemporaryDirectory(prefix="forge-reader-") as directory:
            root = Path(directory)
            database = root / "pedidos.json"
            database.write_text('[{"id":"original","itens":[{"codigo":"Livro","quantidade":1}]}]', encoding="utf-8")
            ready, stop = root / "ready", root / "stop"
            reader = subprocess.Popen([PHP, "-r", reader_code, str(ADAPTER), str(database), str(ready), str(stop)],
                                      stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            try:
                deadline = time.monotonic() + 5
                while not ready.exists():
                    self.assertIsNone(reader.poll(), "Reader failed before writer started")
                    self.assertLess(time.monotonic(), deadline)
                    time.sleep(0.005)
                writer = subprocess.run([PHP, "-r", writer_code, str(ADAPTER), str(database)],
                                        capture_output=True, timeout=10)
                self.assertEqual(0, writer.returncode, writer.stderr)
                self.assertEqual(b"", writer.stderr)
                stop.touch()
                stdout, stderr = reader.communicate(timeout=10)
                self.assertEqual(0, reader.returncode, stderr)
                self.assertEqual(b"", stderr)
                self.assertGreater(int(stdout), 0)
                self.assertEqual(21, len(json.loads(database.read_text(encoding="utf-8"))))
            finally:
                stop.touch()
                if reader.poll() is None:
                    reader.kill()
                reader.communicate(timeout=10)

    def test_distinct_concurrent_orders_are_both_preserved(self):
        codes, records = self.race(["primeiro", "segundo"])
        self.assertEqual([0, 0], codes)
        self.assertEqual({"primeiro", "segundo"}, {record["id"] for record in records})
        self.assertEqual(2, len(records))

    def test_duplicate_is_rejected_inside_locked_write_after_both_prechecks(self):
        codes, records = self.race(["mesmo", "mesmo"])
        self.assertEqual([0, 3], codes)
        self.assertEqual([{"id": "mesmo", "itens": [{"codigo": "Livro", "quantidade": 1}]}], records)


if __name__ == "__main__":
    unittest.main()
