"""Portable DML/expansion examples tested on SQLite, not a Doctrine/MySQL integration."""

from pathlib import Path
import sqlite3
import unittest

ROOT = Path(__file__).resolve().parents[1]
SCHEMA = ROOT / "01-fundamentos/mysql/introducao/exercicios/tarefas-etiquetas.sql"
MIGRATIONS = ROOT / "02-symfony/migrations/exemplos"


class CategoryMigrationTests(unittest.TestCase):
    def setUp(self):
        self.db = sqlite3.connect(":memory:")
        self.addCleanup(self.db.close)
        self.db.executescript(SCHEMA.read_text(encoding="utf-8"))

    def expand(self):
        self.db.executescript((MIGRATIONS / "adicionar-categoria-opcional.sql").read_text(encoding="utf-8"))

    def test_expansion_preserves_existing_data_and_old_insert_shape(self):
        self.db.executemany("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", [(1, "PHP"), (2, "JS")])
        before = self.db.execute("SELECT id, titulo, concluida FROM tarefas ORDER BY id").fetchall()
        self.expand()
        self.assertEqual(before, self.db.execute("SELECT id, titulo, concluida FROM tarefas ORDER BY id").fetchall())
        self.assertEqual([(None,), (None,)], self.db.execute("SELECT categoria FROM tarefas").fetchall())
        self.db.execute("INSERT INTO tarefas(id, titulo) VALUES (3, 'Antigo cliente')")
        self.assertIsNone(self.db.execute("SELECT categoria FROM tarefas WHERE id = 3").fetchone()[0])

    def test_expansion_on_empty_database_accepts_valid_category_and_rejects_blank(self):
        self.expand()
        self.db.execute("INSERT INTO tarefas(id, titulo, categoria) VALUES (1, 'Nova', 'Backend')")
        for value in ["", "   "]:
            with self.assertRaises(sqlite3.IntegrityError):
                self.db.execute("UPDATE tarefas SET categoria = ? WHERE id = 1", (value,))
        self.assertEqual("Backend", self.db.execute("SELECT categoria FROM tarefas").fetchone()[0])


if __name__ == "__main__":
    unittest.main()
