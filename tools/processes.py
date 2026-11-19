"""Run a check in its own process group and stop descendants on interruption."""

import os
import signal
import subprocess


def run_with_timeout(command, *, cwd, timeout):
    options = {"cwd": cwd}
    if os.name == "nt":
        options["creationflags"] = subprocess.CREATE_NEW_PROCESS_GROUP | subprocess.CREATE_NO_WINDOW
    else:
        options["start_new_session"] = True
    with subprocess.Popen(command, **options) as process:
        try:
            return subprocess.CompletedProcess(command, process.wait(timeout=timeout))
        except (subprocess.TimeoutExpired, KeyboardInterrupt):
            try:
                if os.name == "nt":
                    subprocess.run(["taskkill", "/PID", str(process.pid), "/T", "/F"],
                                   stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, timeout=5)
                else:
                    try:
                        os.killpg(process.pid, signal.SIGKILL)
                    except ProcessLookupError:
                        pass
            finally:
                if process.poll() is None:
                    process.kill()
                process.wait()
            raise
