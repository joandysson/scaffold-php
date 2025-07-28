#!/bin/sh

# Start project using Docker and prepare environment
# Usage: sh start.sh

set -e

# Create .env from example if it does not exist
if [ ! -f .env ]; then
  cp .env.example .env
  echo "Environment file created"
fi

# Build and run containers in background
if ! docker compose ps >/dev/null 2>&1; then
  echo "Docker Compose is not available"
  exit 1
fi

docker compose up -d

# Ensure log directory exists with correct permissions
LOG_DIR="storage/logs"
mkdir -p "$LOG_DIR"
chmod -R 775 "$LOG_DIR"

# Install dependencies if vendor directory is missing
if [ ! -d vendor ]; then
  docker exec app composer install
fi
