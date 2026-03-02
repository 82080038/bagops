#!/bin/bash

echo "🐳 Starting BAGOPS Docker Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose is not installed."
    exit 1
fi

# Go to project directory
cd "$(dirname "$0")"

# Create necessary directories
mkdir -p storage/logs
mkdir -p storage/uploads

# Set permissions
chmod -R 755 storage/
chmod -R 777 storage/logs
chmod -R 777 storage/uploads

echo "📁 Creating directories and setting permissions..."

# Start containers
echo "🚀 Building and starting containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "⏳ Waiting for containers to start..."
sleep 10

# Check container status
echo "📊 Checking container status..."
docker-compose ps

# Show URLs
echo ""
echo "✅ BAGOPS Docker Environment is ready!"
echo ""
echo "🌐 Access URLs:"
echo "   • BAGOPS Application: http://localhost/bagops"
echo "   • phpMyAdmin:        http://localhost:8081"
echo "   • MySQL:             localhost:3306"
echo ""
echo "🔧 Useful Commands:"
echo "   • View logs:        docker-compose logs -f"
echo "   • Stop:             docker-compose down"
echo "   • Restart:          docker-compose restart"
echo "   • Shell access:     docker-compose exec app bash"
echo ""
echo "📚 For complete guide, see: DOCKER_SETUP_GUIDE.md"
