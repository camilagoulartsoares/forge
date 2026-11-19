"""Real process-tree timeout regression; all children belong to this test."""

import ctypes
import os
from pathlib import Path
import signal
import subprocess
import sys
import tempfile
import unittest
from unittest.mock import MagicMock, patch

from processes import run_with_timeout


def alive(pid):
    if os.name == "nt":
        kernel = ctypes.windll.kernel32
        kernel.OpenProcess.restype = ctypes.c_void_p
        handle = kernel.OpenProcess(0x100000, False, pid)
        if not handle:
            return False
        try:
            return kernel.WaitForSingleObject(ctypes.c_void_p(handle), 0) == 258
        finally:
            kernel.CloseHandle(ctypes.c_void_p(handle))
    try:
        os.kill(pid, 0)
        state = Path(f"/proc/{pid}/stat")
        return not (state.exists() and state.read_text().split()[2] == "Z")
    except ProcessLookupError:
        return False


class ProcessTimeoutTests(unittest.TestCase):
    def test_timeout_stops_grandchild_instead_of_only_the_leader(self):
        with tempfile.TemporaryDirectory() as directory:
            pidfile = Path(directory) / "child.pid"
            code = "import subprocess,sys,time; from pathlib import Path; child=subprocess.Popen([sys.executable,'-c','import time; time.sleep(60)']); Path(sys.argv[1]).write_text(str(child.pid)); time.sleep(60)"
            pid = None
            try:
                with self.assertRaises(subprocess.TimeoutExpired):
                    run_with_timeout([sys.executable, "-c", code, str(pidfile)], cwd=directory, timeout=3)
                self.assertTrue(pidfile.exists(), "Child did not start before deadline")
                pid = int(pidfile.read_text())
                self.assertFalse(alive(pid), "Grandchild survived timeout")
            finally:
                if pidfile.exists():
                    pid = int(pidfile.read_text())
                if pid and alive(pid):
                    if os.name == "nt":
                        subprocess.run(["taskkill", "/PID", str(pid), "/T", "/F"], capture_output=True, timeout=5)
                    else:
                        os.kill(pid, signal.SIGKILL)

    def test_failed_tree_termination_still_reaps_the_leader(self):
        process = MagicMock()
        process.pid = 123
        process.wait.side_effect = [subprocess.TimeoutExpired(["check"], 1), 0]
        process.poll.return_value = None
        target = "processes.subprocess.run" if os.name == "nt" else "processes.os.killpg"
        with patch("processes.subprocess.Popen") as popen, patch(target, side_effect=OSError("termination failed")):
            popen.return_value.__enter__.return_value = process
            with self.assertRaises(OSError):
                run_with_timeout(["check"], cwd=".", timeout=1)
        process.kill.assert_called_once()
        self.assertEqual(2, process.wait.call_count)

    def test_success_preserves_exit_code(self):
        result = run_with_timeout([sys.executable, "-c", "raise SystemExit(7)"], cwd=Path(__file__).parent, timeout=3)
        self.assertEqual(7, result.returncode)


if __name__ == "__main__":
    unittest.main()
