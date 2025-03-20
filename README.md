# Catalyst PHP Framework

Catalyst is a modern PHP framework designed with flexibility, performance, and developer experience in mind. It combines established architectural patterns with pragmatic solutions to create a robust
foundation for PHP applications.

## Overview

Catalyst is a PHP framework developed by Walter Nuñez ([arcanisgk](https://github.com/arcanisgk)) that combines the best aspects of various architectural patterns to create a flexible yet powerful
development environment. The framework requires PHP 8.3 and is distributed under the GNU Lesser General Public License.

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

### Configuration System

Catalyst employs a multi-layered configuration approach to address different needs:

1. **PHP Constants** - For immutable system parameters that control runtime behavior
2. **JSON Configuration** - For frequently modified settings like credentials and service parameters
3. **Environment Variables** - For deployment-specific settings

Each approach serves a specific purpose, with constants providing IDE support during development, JSON offering easy updates, and environment variables allowing deployment customization.

## Key Components

### Core Framework

- **Routing System** - Flexible URL-to-controller mapping with middleware support
- **View Engine** - Template rendering with layouts and partials
- **Internationalization** - Multi-language support with JSON-based translations
- **Response Handling** - Type-specific responses (HTML, JSON, redirects)
- **Error Management** - Comprehensive error catching, logging, and display

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

1. Define routes in `app/routes/web.php`
2. Create controllers in `app/Repository/Controllers`
3. Add views in `resources/views`
4. Run your application through a web server pointing to the `public` directory

## Development Roadmap

- Service Container implementation
- Database abstraction layer
- Enhanced CLI capabilities
- Administration system for configuration management
- Expanded documentation and tutorials

## License

Catalyst is open-sourced software licensed under the [GNU Lesser General Public License](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
