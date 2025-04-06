# Contributing to Laravel API Starter

Thank you for considering contributing to the Laravel API Starter! This document outlines the guidelines and process for contributing to this project.

## Code of Conduct

This project adheres to a Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## How Can I Contribute?

### Reporting Bugs

- Check if the bug has already been reported in the Issues section
- Use the bug report template when opening a new issue
- Include detailed steps to reproduce the bug
- Provide as much context as possible

### Suggesting Features

- Check if the feature has already been suggested in the Issues section
- Use the feature request template when opening a new issue
- Explain the feature in detail and why it would be valuable

### Pull Requests

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request using the PR template

## Development Setup

1. Clone the repository
2. Install dependencies with `composer install` and `npm install`
3. Copy `.env.example` to `.env` and configure your environment
4. Run migrations with `php artisan migrate`
5. Seed the database with `php artisan db:seed`
6. Start the development server with `php artisan serve`

## Coding Standards

- Follow PSR-12 coding style
- Write tests for new features or bug fixes
- Document any new API endpoints using OpenAPI annotations
- Use Laravel's built-in validation for form requests

## Testing

Run the test suite to ensure your changes don't break existing functionality:

```
php artisan test
```

## Documentation

- Update the README.md if you change functionality
- Document new features with examples
- Add OpenAPI annotations for new API endpoints

## Commit Guidelines

- Use clear and meaningful commit messages
- Reference issues and pull requests when relevant
- Keep commits focused on specific changes

## License

By contributing to Laravel API Starter, you agree that your contributions will be licensed under the project's MIT License. 