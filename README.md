# Catalyst PHP Framework

Catalyst is a modern PHP framework designed with flexibility, performance, and developer experience in mind. It combines established architectural patterns with pragmatic solutions to create a robust
foundation for PHP applications.

## Overview

Catalyst is a PHP framework developed by Walter Nuñez ([arcanisgk](https://github.com/arcanisgk)) that combines the best aspects of various architectural patterns to create a flexible yet powerful
development environment. The framework requires PHP 8.3 and is distributed under the MIT License.

### Philosophy

Catalyst is built on several key principles:

1. **Practical Flexibility** - Use the right pattern for the right job, rather than forcing a single approach
2. **Separation of Concerns** - Clear boundaries between components with single responsibilities
3. **Developer-First Experience** - Intuitive APIs and comprehensive error handling
4. **Framework/Application Separation** - Clear distinction between framework internals and application code

## Architecture

### Dual-Space Design

Catalyst uses a dual-space architecture that explicitly separates framework code from application code:

- **Framework Space** (`app/Assets/Framework/`) - Contains core framework components, not intended for direct modification
- **Application Space** (`app/Repository/`) - Dedicated development area for application-specific code

This separation allows for framework updates without disrupting application code, while still providing all the extension points needed for customization.

### Mixed-Pattern Implementation

Unlike frameworks that rigidly adhere to a single pattern, Catalyst strategically employs multiple architectural patterns where they make the most sense:

- **MVC Pattern** - For structured request handling and response generation
- **Repository Pattern** - For data access abstraction
- **Entity Pattern** - For domain object encapsulation
- **Service Pattern** - For reusable business logic
- **Singleton Pattern** - For services that genuinely need global state

This mixed approach allows developers to use familiar patterns while avoiding their limitations.

### Internationalization

Catalyst provides built-in support for multi-language applications:

- JSON-based translation files organized by language and feature
- Simple translation helpers (`t()` and `__()`)
- Language switching capabilities

### Error Management

The framework includes a comprehensive error management system:

- Detailed error reporting in development environments
- Production-safe error handling
- Customizable error logging and display

## Key Components

### Core Framework

- **Routing System** - Flexible URL-to-controller mapping with middleware support
- **View Engine** - Template rendering with layouts and partials
- **Response Handling** - Type-specific responses (HTML, JSON, redirects)
- **Middleware Stack** - Modular request processing pipeline

### Utility Layer

- **Logging** - Type-categorized logging with formatting support
- **Debugging** - Development tools for inspection and troubleshooting
- **File Operations** - Simplified file system interactions
- **CLI Support** - Command-line interface with dedicated entry point

## Getting Started

### Requirements

- PHP 8.3 or higher
- Composer
- Web server with URL rewriting capability (Apache, Nginx)

### Installation

```bash
composer create-project arcanisgk/catalyst my-project
cd my-project
```

### Basic Usage

1. Define routes in `bootstrap/routes/web.php`
2. Create controllers in `app/Repository/Controllers`
3. Add views in `app/Repository/Views`
4. Run your application through a web server pointing to the `public` directory

## Development Roadmap

- Enhanced database abstraction layer
- Advanced middleware capabilities
- Expanded CLI tools
- Extended documentation and tutorials

## License

Catalyst is open-sourced software licensed under the [MIT License](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

For documentation and more information, visit [catalyst.lh-2.net](https://catalyst.lh-2.net).

