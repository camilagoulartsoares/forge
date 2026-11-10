from pathlib import Path
import sqlite3
import unittest


SCHEMA = Path(__file__).resolve().parents[1] / "01-fundamentos/mysql/introducao/exercicios/tarefas-etiquetas.sql"


class TaskSchemaTests(unittest.TestCase):
    def setUp(self):
        self.db = sqlite3.connect(":memory:")
        self.addCleanup(self.db.close)
        self.db.execute("PRAGMA foreign_keys = ON")
        self.db.executescript(SCHEMA.read_text(encoding="utf-8"))
        self.db.executemany("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", [(1, "Estudar"), (2, "Testar")])
        self.db.execute("INSERT INTO etiquetas(id, nome) VALUES (?, ?)", (1, "PHP"))

    def test_task_without_labels_is_valid(self):
        self.assertEqual([(1, 0), (2, 0)], self.db.execute("SELECT id, concluida FROM tarefas ORDER BY id").fetchall())

    def test_label_can_be_shared_but_relation_cannot_be_duplicated(self):
        self.db.executemany("INSERT INTO tarefa_etiqueta VALUES (?, ?)", [(1, 1), (2, 1)])
        with self.assertRaises(sqlite3.IntegrityError):
            self.db.execute("INSERT INTO tarefa_etiqueta VALUES (1, 1)")
        self.assertEqual(2, self.db.execute("SELECT COUNT(*) FROM tarefa_etiqueta").fetchone()[0])

    def test_missing_foreign_keys_are_rejected(self):
        for relation in [(99, 1), (1, 99)]:
            with self.assertRaises(sqlite3.IntegrityError):
                self.db.execute("INSERT INTO tarefa_etiqueta VALUES (?, ?)", relation)

    def test_delete_restrict_preserves_associated_entities(self):
        self.db.execute("INSERT INTO tarefa_etiqueta VALUES (1, 1)")
        for table in ["tarefas", "etiquetas"]:
            with self.assertRaises(sqlite3.IntegrityError):
                self.db.execute(f"DELETE FROM {table} WHERE id = 1")
        self.assertEqual(1, self.db.execute("SELECT COUNT(*) FROM tarefa_etiqueta").fetchone()[0])

    def test_invalid_status_and_blank_title_are_rejected(self):
        for title, status in [("", 0), ("   ", 0), ("Valida", 2)]:
            with self.assertRaises(sqlite3.IntegrityError):
                self.db.execute("INSERT INTO tarefas VALUES (?, ?, ?)", (3, title, status))

    def test_bound_values_are_data_not_sql(self):
        title = "'); DROP TABLE tarefas; --"
        self.db.execute("INSERT INTO tarefas(id, titulo) VALUES (?, ?)", (3, title))
        self.assertEqual(title, self.db.execute("SELECT titulo FROM tarefas WHERE id = ?", (3,)).fetchone()[0])


if __name__ == "__main__":
    unittest.main()
