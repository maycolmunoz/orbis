<p align="center">
  <img src="./_docs/icon.svg" alt="Orbis Logo" width="80"/>
</p>

# Orbis

**Orbis** is a modular ERP designed for business management.  

---

| Package                     | Version | Description                  |
| --------------------------- | ------- | ---------------------------- |
| Laravel                     | v12     | Core PHP framework           |
| MoonShine                   | v3      | Admin panel                  |
| moonshine-roles-permissions | v3      | Roles and permissions system |
| internachi/modular          | v2      | Modular architecture         |

## 🚀 Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/maycolmunoz/orbis.git
    cd orbis
    ```

2. Set up the environment:

    ```bash
    cp .env.example .env
    composer install
    ```

3. Run the installer:

    ```bash
    php artisan launch:install
    ```

4. Set test data
    ```sh
    php artisan db:seed
    ```
