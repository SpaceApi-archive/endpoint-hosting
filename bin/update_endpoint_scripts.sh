#!/bin/bash

# Commit   9378f7dbdc2d98f4d7ed9547e91eb80f7f8a9d21
# Date:    Sat Feb 22 10:07:59 2014 +0100
# Comment: Dedicated config file created for the api key.

COMMIT=9378f7dbdc2d
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd "$SCRIPT_DIR/../data"
rm -rf endpoint-scripts
git clone https://github.com/SpaceApi/endpoint-scripts.git
cd endpoint-scripts
git checkout $COMMIT
rm -rf .git .gitignore docs screenshots tests README.md index.py