# Agent Instructions

This repository is the **Feature Flipper** WordPress plugin that lets site owners toggle core WordPress features — Comments, Gutenberg, Emojis, XML-RPC, Feeds, Updates, Cron, Heartbeat, and more — on or off from a settings screen.

## Project Shape

- [syntatis-feature-flipper.php](syntatis-feature-flipper.php): the WordPress plugin bootstrap file (plugin header, constants, and bootstrapping).
- [app](app): application classes under `Syntatis\FeatureFlipper\`.
  - [app/Modules](app/Modules): feature groups — `Admin`, `Advanced`, `General`, `Media`, `Security`, `Site`.
  - [app/Features](app/Features): individual feature implementations.
  - [app/Helpers](app/Helpers): static helper classes.
  - [app/Concerns](app/Concerns): traits; [app/Contracts](app/Contracts): interfaces.
- [inc](inc): bootstrap wiring, configuration, settings, and views.
  - [inc/bootstrap](inc/bootstrap): `app.php`, `dev.php`, and `providers.php`.
  - [inc/settings/all.php](inc/settings/all.php): declarative setting definitions.
  - [inc/config/app.php](inc/config/app.php): plugin configuration.
  - [inc/functions](inc/functions): polyfills and other function helpers.
  - [inc/views](inc/views): server-rendered templates.
  - [inc/languages](inc/languages): translation files and the POT file.
- [src](src): front-end source (React/JSX and SCSS) compiled by `@wordpress/scripts` into `dist/assets`.
- [tests/phpunit](tests/phpunit): PHPUnit tests under `Syntatis\Tests\`; paths mirror the `app` structure.
- [vendor](vendor): Composer dependencies. [dist](dist): build output (scoped autoloader and compiled assets).

## General Coding Guidelines

### Language, style, and compatibility

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) plus the `SyntatisWP` coding standard defined in `phpcs.xml.dist`.
- Use `declare(strict_types=1);` in first-party PHP files (the exceptions are `inc/bootstrap/providers.php` and `inc/config/*.php`, which are excluded from the rule).
- Keep PHP code compatible with PHP 7.4+ and WordPress 6.4+.
- Namespaces:
  - `Syntatis\FeatureFlipper\` for product code.
  - `Syntatis\Tests\` for tests.
  - `SFFV\` for scoped third-party classes (PHP-Scoper output).
- Indent PHP with tabs (width 4) and JSON/Markdown/NEON/YAML with spaces (width 2); see `.editorconfig`.
- Internationalize user-facing strings with the `syntatis-feature-flipper` text domain.

### Architecture and testability

- Prefer small, direct changes in the owning module or feature class.
- Only add an abstraction when it removes real duplication or matches an existing local pattern.
- Only add an interface when there is a real boundary with more than one implementation.
- Prefer injecting a concrete service owned by this project; resolve dependencies from the PSR-11 container (`SFFV\Psr\Container\ContainerInterface`).
- Classes that use I/O (filesystem, HTTP, WP-CLI) should accept those collaborators instead of creating them internally.

### Codex framework conventions

The plugin is built on the [Codex framework](https://

- Hooks: implement `SFFV\Codex\Contracts\Hookable` and register callbacks in `hook(Hook $hook)` with `$hook->addAction()` / `$hook->addFilter()`.
- Composition: implement `SFFV\Codex\Contracts\Extendable` and return child instances from `getInstances(ContainerInterface $container)` using `yield`.
- Modules (`app/Modules/*`) are `Hookable` and `Extendable`: they register their own hooks and yield their feature instances.
- Settings: define options declaratively in `inc/settings/all.php` using `SFFV\Codex\Settings\Setting`.
- Options: read and write options through `Syntatis\FeatureFlipper\Helpers\Option` (`get`, `isOn`, `update`, `add`, `delete`).
- Static-only helper classes use the `Syntatis\FeatureFlipper\Concerns\DontInstantiate` trait.
- Use the `SFFV\Codex\Facades\App` / `Config` facades for paths, URLs, and config instead of hard-coding them.

### WordPress security and context

- In plugin code, preserve WordPress security practices:
  - Validate and sanitize input.
  - Escape output.
  - Check capabilities and nonces where applicable.
  - Prefer WordPress APIs for URLs, redirects, options, hooks, and plugin metadata.
- Validate data at the boundary and escape only when emitting output.

### Error handling

- Fail gracefully and fall back to a safe default (e.g., return the original value from a filter callback).
- Use `try/catch` around code that can throw; catch `Throwable` where a broad guard is appropriate.
- If a function or method can throw an exception, document it in the docblock with `@throws`.

## Tooling

The project use Composer and npm scripts.

PHP (requires PHP 7.4+ locally):

- `composer install` — install PHP dependencies.
- `composer lint` — check coding standards with PHPCS.
- `composer format` — auto-fix coding-standard violations with PHPCBF.
- `composer analyze` — run PHPStan static analysis (level 10).
- `composer make-pot` — regenerate `inc/languages/syntatis-feature-flipper.pot`.
- `composer scope` — regenerate the scoped dependencies (PHP-Scoper).
- `composer build` — build the production plugin (scope + POT).

JavaScript/React (See `.nvmrc` for the Node.js version):

- `npm install` — install front-end dependencies.
- `npm run build` — production build into `dist/assets`.
- `npm start` — watch/dev build into `dist/assets`.
- `npm run lint:js`, `npm run lint:css` — lint JavaScript and SCSS.
- `npm run format` — format JavaScript/SCSS.

Local WordPress environment (`@wordpress/env`):

- `npm run wp-env:start` — start the site (http://localhost:8801) and tests environment (port 8901).
- `npm run wp-env:tests-wordpress` — run PHPUnit inside the tests environment.
- `npm run wp-env:destroy` — tear down the environment.

## Testing and validation

- PHPUnit configuration lives in `phpunit.xml.dist` (testsuite `app` under `tests/phpunit/app/`, bootstrap `tests/phpunit/bootstrap.php`).
- Test classes extend `Syntatis\Tests\WPTestCase` (a `WP_UnitTestCase` wrapper) and live under `tests/phpunit/app/`, mirroring `app/` — e.g. `app/Features/Comments.php` is covered by `tests/phpunit/app/Features/CommentsTest.php`.
- Run tests inside the WordPress test environment: `npm run wp-env:tests-wordpress`.
- After changing production PHP, run the narrowest useful test first, then broader validation:
  - Coding standards: `composer lint` (or `composer format` to auto-fix).
  - Static analysis: `composer analyze`.
  - Scoping or release packaging: `composer scope` or `composer build`.
