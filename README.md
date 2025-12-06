# Sistema de Gestión de Producción e Inventarios

Este es un sistema web desarrollado en **PHP** y **MySQL** diseñado para gestionar el ciclo completo de manufactura de una empresa de productos de limpieza (o similar). Permite controlar desde la compra de materia prima y la formulación de recetas, hasta la producción de lotes, el control de stock y la venta final.

## 📋 Características Principales

### 1. Control de Acceso y Usuarios
* **Autenticación**: Sistema de Login/Logout seguro.
* **Roles de Usuario**:
    * **Admin**: Acceso total (CRUD de usuarios, reportes financieros, configuraciones).
    * **Administrativo/Empleado**: Acceso operativo limitado.
* **Gestión de Empleados**: Registro detallado de información personal y credenciales de acceso.

### 2. Inventarios
* **Materias Primas**: Catálogo de insumos con control de existencias mínimas/máximas y búsqueda avanzada.
* **Productos Terminados**: Catálogo de productos listos para la venta.
* **Movimientos**:
    * Registro de **Entradas** de materia prima (compras) con costos.
    * Registro de **Salidas** de producto (ventas) con precios de venta.

### 3. Módulo de Producción (MRP)
* **Formulaciones (Recetas)**: Definición de ingredientes y cantidades exactas necesarias para fabricar un producto.
* **Producción**: Lógica automática que descuenta la materia prima del inventario y aumenta el stock del producto terminado basándose en la formulación.
* **Validación de Stock**: El sistema impide producir si no hay suficientes insumos.
* **Historial**: Registro de lotes producidos por fecha y empleado responsable.

### 4. Reportes Inteligentes
* **Análisis de Costos**: Calcula automáticamente el costo de producción por kilo basándose en el promedio histórico de costos de las materias primas.
* **Márgenes de Ganancia**: Compara el costo de producción vs. el precio de venta promedio para estimar la utilidad.

## 🛠️ Tecnologías Utilizadas

* **Backend**: PHP (Nativo, sin frameworks).
* **Base de Datos**: MySQL / MariaDB.
* **Frontend**: HTML5, CSS3.
* **Framework CSS**: [Pico.css](https://picocss.com/) (Versión Classless) para un diseño limpio y responsivo.
* **Estilos Personalizados**: `estilos.css`.

## ⚙️ Instalación y Configuración

### 1. Requisitos Previos
* Un servidor web (XAMPP, Laragon, WAMP o LAMP).
* PHP 7.4 o superior.
* MySQL 5.7 o superior.

### 2. Base de Datos
Crea una base de datos llamada `productos_limpieza` con las tablas necesarias.

### 3\. Conexión

Abre el archivo `conn.php` y configura tus credenciales de base de datos:

```php
<?php
    $conn = mysqli_connect('localhost', 'root', 'TU_CONTRASEÑA', 'productos_limpieza');
    // ...
?>
```

### 4\. Ejecución

1.  Coloca la carpeta del proyecto en tu directorio `htdocs` o `www`.
2.  Accede desde el navegador a `http://localhost/nombre_carpeta`.
3.  Inicia sesión con el usuario admin creado en el paso 2.

## 📖 Guía de Uso Rápida

1.  **Alta de Insumos**: Ve a *Inventario \> Materias Primas* y registra tus ingredientes base.
2.  **Compras**: Ve a *Movimientos \> Materias Primas* para registrar entradas y aumentar el stock de insumos.
3.  **Alta de Productos**: Ve a *Inventario \> Productos* y crea los productos que venderás.
4.  **Recetas**: Ve a *Producción \> Formulaciones*, busca un producto y asigna qué materias primas lleva y en qué cantidad.
5.  **Fabricación**: Ve a *Producción \> Mandar a Producción*. Selecciona el producto y la cantidad. El sistema descontará los insumos y creará el producto.
6.  **Ventas**: Ve a *Movimientos \> Productos* para registrar salidas y generar ingresos.
7.  **Análisis**: Ve a *Reportes* para ver la rentabilidad de tus productos.
