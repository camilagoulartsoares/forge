"""Compare the real SQL summary column contract with the JavaScript derived state."""

import json
from pathlib import Path
import shutil
import sqlite3
import subprocess
import unittest

ROOT = Path(__file__).resolve().parents[1]
SQL = ROOT / '01-fundamentos/mysql/introducao/exercicios'
NODE = """
import { readFileSync } from 'node:fs';
import { resumirTarefas } from './03-vue/pinia/resumo-tarefas.mjs';
process.stdout.write(JSON.stringify(resumirTarefas(JSON.parse(readFileSync(0, 'utf8')))));
"""


@unittest.skipUnless(shutil.which('node'), 'Node CLI required')
class SummaryContractTests(unittest.TestCase):
    def test_sql_summary_names_and_counts_match_derived_state_across_status_changes(self):
        db = sqlite3.connect(':memory:')
        self.addCleanup(db.close)
        db.execute('PRAGMA foreign_keys = ON')
        db.executescript((SQL / 'tarefas-etiquetas.sql').read_text(encoding='utf-8'))
        summary = (SQL / 'resumir-tarefas.sql').read_text(encoding='utf-8')
        for states in [[], [0, 0, 0], [0, 1, 0], [1, 1, 1]]:
            with self.subTest(states=states):
                db.execute('DELETE FROM tarefas')
                db.executemany('INSERT INTO tarefas VALUES (?, ?, ?)', [(i + 1, 'Tarefa', state) for i, state in enumerate(states)])
                rows = db.execute('SELECT id, titulo, concluida FROM tarefas ORDER BY id').fetchall()
                tasks = [dict(id=id, titulo=title, concluida=bool(completed)) for id, title, completed in rows]
                cursor = db.execute(summary)
                sql_result = dict(zip([column[0] for column in cursor.description], cursor.fetchone()))
                result = subprocess.run(['node', '--input-type=module', '-e', NODE], input=json.dumps(tasks),
                                        capture_output=True, text=True, cwd=ROOT, timeout=10)
                self.assertEqual(0, result.returncode, result.stderr)
                self.assertEqual('', result.stderr)
                self.assertEqual(sql_result, json.loads(result.stdout))


if __name__ == '__main__':
    unittest.main()
