
# 1. Instalar dependencias de Composer
composer install

# 2. Instalar dependencias de NPM (si usa frontend)
npm install

# 3. Copiar el archivo de entorno
cp .env.example .env

# 4. Generar la clave de aplicación
php artisan key:generate

# 5. Configurar la base de datos en .env
# Edita el archivo .env con tus credenciales:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nombre_bd
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Ejecutar migraciones
php artisan migrate

# 7. (Opcional) Ejecutar seeders para datos de prueba
php artisan db:seed

# 8. (Opcional) Crear enlace simbólico para storage
php artisan storage:link

# 9. (Opcional) Compilar assets frontend
npm run dev
o para producción:
npm run build

# 11. Iniciar el servidor de desarrollo
php artisan serve
