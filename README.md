## Pasos para instalar el proyecto.
1. Clonar    
    ```git clone https://github.com/inferiore/cash-register.git```
    
2. Instalar dependencias:  
    `cd cash-register`
    
    `composer install`
  
3. Instalar Extencion de Php Para SQLite3:
   
   ```sudo apt-get install php[7.2]-sqlite3 (Escribir la version de php para la cual usted esta usando)```
4. Migrar la base de datos: 
    
    ```php artisan migrate``` 
5. Poblar la base de datos
 
    ```php artisan db:seed ```
6. Dump autoload

    ```composer dump-autoload ```
    
## Levantar el servido de prueba. 
1. Ya instalado el proyecto solo nos queda levantar el servidor
    ``` php artisan serve```
 
## Endpoints.

Hay dos colecciones de postman en la raíz del proyecto, estas deben ser exportadas a postman debido a que tiene ejemplos sobre como utilizar los endpoints. Una es para las variables de entorno y la otra es para los endpoints.  
