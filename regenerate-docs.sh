#!/bin/bash

# Script to regenerate API documentation
echo "Regenerating API documentation..."

# Clear previous cache
php artisan optimize:clear

# Generate L5 Swagger documentation
php artisan l5-swagger:generate

echo "Documentation regenerated successfully!"
echo "You can access the documentation at: http://localhost:8000/api/documentation" 