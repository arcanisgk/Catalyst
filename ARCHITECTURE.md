# Catalyst Framework Architecture

This document describes the architectural approach of the Catalyst PHP Framework, with a focus on the separation between framework internals and application code.

## Dual-Space Architecture

Catalyst employs a "dual-space" architecture that explicitly separates framework code from application code. This separation creates clear boundaries and responsibilities while still allowing for
maximum flexibility.

### Framework Space

**Location:** `app/Assets/Framework/`

**Purpose:** Contains core framework components that should not be directly modified by application developers.

**Characteristics:**

- Provides foundational services and base classes
- Implements core interfaces and abstractions
- Handles low-level framework operations
- Maintains internal consistency and backward compatibility
- Updates can be applied without breaking application code

### Application Space

**Location:** `app/Repository/`

**Purpose:** Dedicated development area for application-specific code and third-party integrations.

**Characteristics:**

- Contains application-specific business logic
- Extends and customizes framework components
- Safe for developers to modify without affecting framework internals
- Persists through framework updates
- Follows application-specific organizational patterns

## Components Architecture

### Core Components

#### Routing System

The routing system in Catalyst follows a pipeline architecture:

- **RouteCollection** - Stores and organizes routes
- **RouteCompiler** - Transforms route patterns into matchable patterns
- **RouteDispatcher** - Matches incoming requests with routes
- **Middleware Stack** - Processes requests through middleware before reaching controllers

#### View System

Templates are processed through several layers:

- **ViewFinder** - Locates template files based on naming conventions
- **LayoutManager** - Applies master layout templates
- **ViewRenderer** - Processes templates with data bindings
- **ViewResponse** - Wraps rendered content in HTTP responses

#### Translation System

Internationalization is managed through:

- **TranslationManager** - Coordinates loading and caching translations
- **TranslationService** - Provides translation lookup functionality
- **TranslationCache** - Optimizes performance for translation lookups

### Error Handling

Error management follows a hierarchical approach:

- **BugCatcher** - Central exception and error trapping mechanism
- **ErrorHandler** - Processes PHP errors
- **ExceptionHandler** - Processes uncaught exceptions
- **ShutdownHandler** - Handles fatal errors during PHP shutdown
- **BugLogger** - Records errors to appropriate logs
- **BugOutput** - Formats error output for display

## Extension vs. Override Guidelines

### When to Extend Framework Components

**Extend** framework classes when:

1. **Adding new functionality** to an existing component
   ```php
   // Example: Extending base controller with application-specific methods
   namespace App\Repository\Controllers;
   
   use Catalyst\Framework\Controllers\Controller;
   
   class ProductController extends Controller
   {
       // New application-specific methods
       public function catalog()
       {
           return $this->view('products.catalog', ['products' => $this->getProducts()]);
       }
   }
   ```

2. **Specializing behavior** while maintaining core functionality
   ```php
   // Example: Specializing a base entity
   namespace App\Repository\Entities;
   
   use Catalyst\Entity\Default\User as BaseUser;
   
   class User extends BaseUser
   {
       // Add application-specific user properties and methods
       protected array $preferences = [];
       
       public function getPreferences(): array
       {
           return $this->preferences;
       }
   }
   ```

3. **Implementing abstract methods** or interfaces defined by the framework
   ```php
   // Example: Implementing a middleware
   namespace App\Repository\Middleware;
   
   use Catalyst\Assets\Framework\Core\Middleware\MiddlewareInterface;
   
   class AuthenticationMiddleware implements MiddlewareInterface
   {
       public function process($request, $next)
       {
           // Implementation of the interface method
           if (!isAuthenticated()) {
               return redirect('/login');
           }
           
           return $next($request);
       }
   }
   ```

### When to Override Framework Components

**Override** framework components when:

1. **Replacing the entire implementation** of a framework concept
   ```php
   // Example: Completely replacing how authentication works
   namespace App\Repository\Services;
   
   use Catalyst\Assets\Framework\Auth\AuthInterface;
   
   class CustomAuthentication implements AuthInterface
   {
       // Completely custom implementation
   }
   
   // In your service provider:
   $container->bind(AuthInterface::class, CustomAuthentication::class);
   ```

2. **Intercepting core behavior** that cannot be modified through extension
   ```php
   // Example: Intercepting and modifying route resolution
   namespace App\Repository\Routing;
   
   use Catalyst\Assets\Framework\Core\Route\Router as BaseRouter;
   
   class CustomRouter extends BaseRouter
   {
       public function dispatch($request)
       {
           // Custom pre-processing
           $request = $this->transformRequest($request);
           
           // Call parent implementation
           return parent::dispatch($request);
       }
   }
   ```

## Configuration Layers

Catalyst uses a three-layer configuration approach:

1. **PHP Constants** - For immutable framework parameters
2. **JSON Configuration** - For application settings that change between environments
3. **Environment Variables** - For deployment-specific settings

This layered approach provides both flexibility and performance, with constants being the fastest but least flexible, and environment variables being the most flexible but requiring runtime
processing.

## Best Practices

1. **Never modify framework files directly** - Always extend or override instead
2. **Use interfaces for integration** - Depend on interfaces rather than concrete implementations
3. **Create bridges when needed** - If integration is complex, create bridge classes
4. **Maintain namespace separation** - Keep `App\Assets\Framework` and `App\Repository` namespaces distinct
5. **Follow framework conventions** - Match the naming and structural patterns of the framework
6. **Use composition over deep inheritance** - Prefer delegating to framework services rather than creating deep inheritance chains

## Framework Update Strategy

When updating the framework:

1. New versions will be distributed as updates to the `app/Assets/Framework` directory
2. Application code in `app/Repository` remains untouched
3. Update scripts will preserve any custom configuration

By maintaining this separation, Catalyst provides both stability and flexibility, allowing the framework to evolve while preserving application customizations.