# TECHNOJOB (un motor de búsqueda de empleo tecnológico)

Este proyecto es un ejercicio didactico para la creación de una aplicación para busquedas de empleo.
Esta app es un motor de busquedas de ofertas de trabajo online. Trabajadores y empresas pueden registrarse en interactuar entre sí a través de ofertas de empleo, suscribiendose a ellas los primeros y aceptando o rechazando candidaturas los segundos.

## Comenzando 🚀

Para instalar el programa y probarlo necesitarás descargar los archivos json para crear la base de datos, la parte de 
back, que aparece en el enlace más abajo.

Mira **Deployment** para conocer como desplegar el proyecto.

### Pre-requisitos 📋

Necesitas tener instalado Composer, y todas las dependencias suministradas en el package.json tanto del back como del front.

Para hacer uso de la base de datos, debe tener instalado algun sistema de gestion de bases de datos tipo SQL (Mysql, MariaDB).
```
https://getcomposer.org/

```
```
https://github.com/RodXIII/technojob-backend.git (Repositorio donde se encuentra el Back del proyecto)
https://github.com/perisdev/technojob-frontend.git (Repositorio donde se encuentra el Front del proyecto)
```

### Instalación 🔧

Una vez descargado este repositorio e instalado Composer en su equipo, desde una consola de comandos entre en el directorio y esriba en ella:


```
$ composer install
```


## Despliegue 📦

Una vez instaladas las dependencias, se procedera a crear y llenar las tablas 
de la base de datos. Para conseguir esto, en una consola de comandos, entramos en el directorio del proyecto y tecleamos: 

```
php artisan migrate && php artisan db:seed
```
Una vez creada y sembrada la base de datos, debemos levantar el back, tecleando en una consola de comandos en su directorio:

```
$ php artisan serve
```

De este modo la aplicacion estará corriendo de forma local en el puerto 8000.

## Construido con 🛠️

Para desarrollar este proyecto se han utilizado las siguientes tecnologías:

* [php](https://www.php.net/) - Lenguaje de desarrollo
* [laravel](https://laravel.com/) - Framework utilizado
* [eloquent](https://laravel.com/docs/5.8/eloquent) - ORM
* [MySQL](https://www.mysql.com/) - Sistema de gestion de bases de datos Sql
* [Git](https://git-scm.com/) - Sistema de control de versiones

## Autores ✒️

* **Santiago Peris** - *Desarrollador* - [perisdev](https://github.com/perisdev)
* **Rodrigo Navarro** - *Desarollador* - [RodXIII](https://github.com/RodXIII)

## Licencia 📄

Este proyecto está bajo la Licencia (Open Source) 

---