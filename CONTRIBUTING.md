# Contributing Guidelines

## Code Standards

This project follows PSR-12 coding standards.

### PHP Code Style

```bash
# Check code style
vendor/bin/pint --test

# Fix code style
vendor/bin/pint
```

## Development Setup

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Make your changes
4. Write tests for new functionality
5. Run tests: `php artisan test`
6. Commit with meaningful messages
7. Push to your fork
8. Submit a Pull Request

## Commit Messages

Use conventional commit format:

```
feat: add new feature
fix: fix a bug
docs: update documentation
test: add tests
refactor: refactor code
style: formatting changes
perf: performance improvements
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/InvoiceTest.php

# Run with coverage
php artisan test --coverage
```

## Pull Request Process

1. Update documentation if needed
2. Add tests for new features
3. Ensure all tests pass
4. Update CHANGELOG.md
5. Follow the PR template
6. Ensure at least one approval before merge

## Reporting Issues

- Use GitHub Issues
- Provide detailed description
- Include steps to reproduce
- Add relevant code snippets
- Specify your environment

## Security

If you find a security vulnerability, please email security@example.com instead of using the issue tracker.

## License

All contributions are licensed under the MIT License.
