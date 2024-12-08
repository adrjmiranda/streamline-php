# Streamline PHP

**StreamlinePHP** is a minimalist and modular framework built in pure PHP, designed to provide a lightweight yet solid foundation for modern web application development. With a focus on simplicity and efficiency, StreamlinePHP offers essential features like routing, controller management, database integration, templating system, and middleware support, allowing for a flexible and easily extensible MVC architecture.

Ideal for developers seeking complete control over application flow, StreamlinePHP balances simplicity and functionality, enabling you to build scalable and secure applications quickly and without unnecessary overhead.

---

## **Instalação**

To get started with Streamline PHP, you can install it directly with Composer:

```bash
composer create-project adrjmiranda/streamline-php
```

**StreamlinePHP** is the perfect choice for those who value efficiency and flexibility in a pure PHP environment.

---

## Features

### Streamline PHP currently offers the following systems and tools:

- [Route System]()
- [Template System]()
- [Session Management]()
- [Cache System]()
- [Log System]()
- [Validations]()
- [Middleware]()
- [Query Builder with Database]()

Each of these features is detailed in the [project's Wiki]().

---

## Exemplo Básico

#### Aqui está um exemplo de uso básico do framework:

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Streamline\Bootstrap;
use Streamline\Core\Router;

use App\Controllers\HomeController;

use App\Middlewares\CacheMiddleware;

Bootstrap::initialize();

$router = new Router();

$router->get('/', HomeController::class . ':index')->addMiddleware(CacheMiddleware::class)->alias('home_page');

$router->run();

```

---

## Contribution

#### Want to contribute? Follow these steps:

- Fork the repository.
- Create a branch for your changes: git checkout -b feature/my-improvement.
- Push your changes: git push origin feature/my-improvement.
- Open a Pull Request on the main repository.

---

## Licença

Este projeto é licenciado sob a [Licença MIT](LICENSE).

---

## Autor

Desenvolvido com 💻 e ☕ por [Adriano Miranda](https://github.com/adrjmiranda).
