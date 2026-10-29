#!/usr/bin/env python3
"""Compare the simulator command catalogue with APPManager::parseJsonInput()."""
from __future__ import annotations

import argparse
import json
import re
from pathlib import Path


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("firmware_root", type=Path, help="Path to the ESP32 repository")
    parser.add_argument("--catalog", type=Path, default=Path(__file__).with_name("commands.json"))
    args = parser.parse_args()

    source = args.firmware_root / "lib/Interval/src/APPManager.h"
    if not source.is_file():
        parser.error(f"APPManager.h not found: {source}")

    code = source.read_text(encoding="utf-8")
    firmware = set(re.findall(r'command\s*==\s*"([^"]+)"', code))
    # INTERFACE_CONFIG is a macro, but its public command is deviceConfig.
    firmware.add("deviceConfig")
    catalog = json.loads(args.catalog.read_text(encoding="utf-8"))
    listed = {entry["name"] for entry in catalog.get("commands", [])}
    mock_only = {entry["name"] for entry in catalog.get("commands", []) if entry.get("mockOnly")}

    missing = sorted(firmware - listed)
    extra = sorted(listed - firmware - mock_only)
    print("Firmware commands:", ", ".join(sorted(firmware)))
    print("Catalogue commands:", ", ".join(sorted(listed)))
    if missing:
        print("Missing from catalogue:", ", ".join(missing))
    if extra:
        print("Not found in firmware source:", ", ".join(extra))
    return 1 if missing or extra else 0


if __name__ == "__main__":
    raise SystemExit(main())
