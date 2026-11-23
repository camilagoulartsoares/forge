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
        self.db.execute("PRAGMA foreign_keys = ON")
        self.db.executescript(SCHEMA.read_text(encoding="utf-8"))

    def test_expansion_and_backfill_preserve_shared_label_relationships(self):
        self.db.executemany("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", [(1, 'PHP'), (2, 'JS')])
        self.db.execute("INSERT INTO etiquetas VALUES (1, 'Estudos')")
        self.db.executemany("INSERT INTO tarefa_etiqueta VALUES (?, ?)", [(1, 1), (2, 1)])
        self.db.commit()
        self.expand()
        sql = (MIGRATIONS / 'preencher-categoria.sql').read_text(encoding='utf-8')
        self.db.execute(sql, {'id': 1, 'categoria': 'Backend'})
        self.assertEqual([(1, 1), (2, 1)], self.db.execute('SELECT * FROM tarefa_etiqueta ORDER BY tarefa_id').fetchall())
        self.assertEqual([], self.db.execute('PRAGMA foreign_key_check').fetchall())
        with self.assertRaises(sqlite3.IntegrityError):
            self.db.execute('DELETE FROM tarefas WHERE id = 1')
        with self.assertRaises(sqlite3.IntegrityError):
            self.db.execute('INSERT INTO tarefa_etiqueta VALUES (999, 1)')
        self.assertEqual([('Backend',), (None,)], self.db.execute('SELECT categoria FROM tarefas ORDER BY id').fetchall())

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

    def test_backfill_is_bound_repeatable_and_preserves_existing_classification(self):
        self.expand()
        self.db.executemany("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", [(1, "PHP"), (2, "JS")])
        sql = (MIGRATIONS / "preencher-categoria.sql").read_text(encoding="utf-8")
        self.assertEqual(1, self.db.execute(sql, {"id": 1, "categoria": "Backend"}).rowcount)
        self.assertEqual(0, self.db.execute(sql, {"id": 1, "categoria": "Outra"}).rowcount)
        self.assertEqual(0, self.db.execute(sql, {"id": 999, "categoria": "Backend"}).rowcount)
        value = "'); DROP TABLE tarefas; --"
        self.db.execute(sql, {"id": 2, "categoria": value})
        self.assertEqual([(1, "Backend"), (2, value)], self.db.execute("SELECT id, categoria FROM tarefas ORDER BY id").fetchall())

    def test_failed_backfill_batch_rolls_back_prior_updates(self):
        self.expand()
        self.db.executemany("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", [(1, "PHP"), (2, "JS")])
        self.db.commit()
        sql = (MIGRATIONS / "preencher-categoria.sql").read_text(encoding="utf-8")
        with self.assertRaises(sqlite3.IntegrityError):
            with self.db:
                self.db.execute(sql, {"id": 1, "categoria": "Backend"})
                self.db.execute(sql, {"id": 2, "categoria": ""})
        self.assertEqual([(None,), (None,)], self.db.execute("SELECT categoria FROM tarefas ORDER BY id").fetchall())

    def test_read_only_preflight_lists_remaining_backfill_work_in_stable_order(self):
        self.expand()
        self.db.executemany("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", [(9, 'JS'), (1, 'PHP')])
        sql = (MIGRATIONS / 'verificar-categorias-pendentes.sql').read_text(encoding='utf-8')
        backfill = (MIGRATIONS / 'preencher-categoria.sql').read_text(encoding='utf-8')
        before = self.db.total_changes
        self.assertEqual([(1, 'PHP'), (9, 'JS')], self.db.execute(sql).fetchall())
        self.assertEqual(before, self.db.total_changes)
        self.db.execute(backfill, {'id': 1, 'categoria': 'Backend'})
        self.assertEqual([(9, 'JS')], self.db.execute(sql).fetchall())
        self.db.execute(backfill, {'id': 9, 'categoria': 'Frontend'})
        self.assertEqual([], self.db.execute(sql).fetchall())
        self.db.execute("INSERT INTO tarefas(id, titulo) VALUES (10, 'Cliente antigo')")
        self.assertEqual([(10, 'Cliente antigo')], self.db.execute(sql).fetchall())

    def test_null_category_does_not_report_a_filled_row(self):
        self.expand()
        self.db.execute("INSERT INTO tarefas(id, titulo) VALUES (1, 'Pendente')")
        sql = (MIGRATIONS / "preencher-categoria.sql").read_text(encoding="utf-8")
        with self.assertRaises(sqlite3.IntegrityError):
            self.db.execute(sql, {"id": 1, "categoria": None})
        self.assertIsNone(self.db.execute("SELECT categoria FROM tarefas").fetchone()[0])
        self.assertEqual(1, self.db.execute(sql, {"id": 1, "categoria": "Backend"}).rowcount)


if __name__ == "__main__":
    unittest.main()
