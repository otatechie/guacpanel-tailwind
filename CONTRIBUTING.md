# Contributing to GuacPanel

Thank you for considering contributing to GuacPanel! We welcome contributions from the community.

## How to Contribute

### Reporting Issues
- Use the GitHub issue tracker
- Provide clear description and steps to reproduce
- Include your environment details (PHP, Node.js versions)

### Submitting Changes
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Test your changes thoroughly
5. Commit with clear messages (`git commit -m 'Add amazing feature'`)
6. Push to your branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Development Setup
Follow the installation instructions in the README.md, then:

```bash
# Install development dependencies
npm install
composer install

# Run tests
php artisan test
npm run test

# Start development
npm run dev
php artisan serve
```

### Code Style
- Follow PSR-12 for PHP code
- Use Prettier for JavaScript/Vue formatting
- Write descriptive commit messages
- Add tests for new features

## Questions?

Feel free to open an issue for questions or join our discussions! 