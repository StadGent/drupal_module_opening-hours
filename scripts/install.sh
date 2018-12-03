#!/bin/bash

# Check if yarn is installed on your system.
if ! [ -x "$(command -v npm)" ]; then
  echo 'Error: NPM is not installed. Please install NPM globally to execute this script.' >&2
  exit 1
fi

# Install dependencies
echo "Installing NPM dependencies...";
npm install
