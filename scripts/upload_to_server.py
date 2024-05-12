#! /usr/bin/env python3
"""
CLI tool to copy a directory or file to the FUT server.

Basically a wrapper over rsync. Useful for deploying code.
"""

from argparse import ArgumentParser
import subprocess

# TODO: Support other people's usernames once other people need this script.
USERNAME_ON_SERVER = "veliebm"
SERVER_IP = "172.16.1.2"
UPLOADS_DIRECTORY_ON_SERVER = "~/uploads"


def main() -> None:
    parser = ArgumentParser()
    parser.add_argument(
        "target",
        help="What to upload.",
    )
    args = parser.parse_args()

    upload(args.target)


def upload(target_dir: str) -> None:
    command = [
        "rsync",
        "--archive",
        "--relative",
        target_dir,
        f"{USERNAME_ON_SERVER}@{SERVER_IP}:{UPLOADS_DIRECTORY_ON_SERVER}",
    ]
    print(f"Running:\n{command}")
    print(subprocess.run(command, check=True))


if __name__ == "__main__":
    main()
