"""Regression checks for the portable SELECT examples, not a MySQL integration suite."""

from pathlib import Path
import sqlite3
import unittest


EXAMPLES = Path(__file__).resolve().parents[1] / "01-fundamentos/mysql/introducao/exemplos"


class SqlExamplesTests(unittest.TestCase):
    def query(self, name, before=None, after=None):
        sql = (EXAMPLES / name).read_text(encoding="utf-8")
        if before is not None:
            self.assertIn(before, sql)
            sql = sql.replace(before, after)
        with sqlite3.connect(":memory:") as connection:
            return connection.execute(sql).fetchall()

    def test_left_join_preserves_unmatched_customer(self):
        self.assertEqual([(1, "Ana", "Livro"), (2, "Bruno", None)], self.query("juncoes.sql"))

    def test_inner_join_removes_unmatched_customer(self):
        self.assertEqual([(1, "Ana", "Livro")], self.query("juncoes.sql", "LEFT JOIN", "INNER JOIN"))

    def test_having_filters_groups(self):
        self.assertEqual([("Sul", 2, 50)], self.query("agregacao.sql"))

    def test_without_having_keeps_all_groups(self):
        self.assertEqual([("Norte", 1, 10), ("Sul", 2, 50)], self.query("agregacao.sql", "HAVING COUNT(*) >= 2", ""))

    def test_is_null_and_coalesce(self):
        self.assertEqual([(2, "sem email")], self.query("valores-nulos.sql"))

    def test_equality_with_null_is_not_is_null(self):
        self.assertEqual([], self.query("valores-nulos.sql", "IS NULL", "= NULL"))


if __name__ == "__main__":
    unittest.main()
