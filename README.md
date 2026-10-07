# Proyecto-pagina-tipo-amazon
# E-commerce Marketplace Project

Academic e-commerce web application inspired by large marketplace platforms.

The project was developed with PHP, MySQL, HTML, CSS and JavaScript and includes the main workflows of a simple online store.

## Main features

- User registration and login
- User profile
- Product catalog
- Product detail pages
- Product categories
- Shopping cart
- Add and remove products from the cart
- Session-based user flows
- MySQL database integration

## Tech stack

<p>
  <a href="https://skillicons.dev">
    <img src="https://skillicons.dev/icons?i=php,mysql,html,css,js&theme=dark" />
  </a>
</p>

`PHP` · `MySQL` · `HTML` · `CSS` · `JavaScript`

## Project structure

```text
Pagina/
├── Config/       Database configuration
├── Css/          Stylesheets
├── Images/       Project assets
├── Includes/     Shared PHP components
├── Js/           JavaScript
├── Public/       Application pages and actions
└── Sql/          Database scripts
```

The `Public` directory contains pages and actions such as:

```text
index.php
login.php
register.php
perfil.php
producto.php
categorias.php
carrito.php
agregar_carrito.php
quitar_carrito.php
Logout.php
```

## Database setup

The SQL scripts are located in:

```text
Pagina/Sql/
```

Import them in this order:

```text
1. crear_tablas.sql
2. productos_mxn.sql
3. categorias.sql
```

Then configure the database connection in:

```text
Pagina/Config/db.php
```

## Run locally

The project can be run using a local PHP and MySQL environment such as XAMPP or WAMP.

```bash
git clone https://github.com/POXTRZ/Proyecto-pagina-tipo-amazon.git
```

Then:

1. Move the project to your local web server directory.
2. Create the MySQL database.
3. Import the SQL scripts.
4. Configure `Pagina/Config/db.php`.
5. Open the application through your local PHP server.

## Project context

This repository was created as a collaborative academic project to practice full-stack web development, database integration and common e-commerce workflows.
