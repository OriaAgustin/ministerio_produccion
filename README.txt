[COMO USAR]

PASO 1: Crear la imagen a partir del Dockerfile:

docker build -t agustinoria/ministerio_produccion .

• PASO 2: Crear un container a partir de la imagen generada por el Dockerfile:

Container sin volumen:
docker run -d -p 80:80 -p 3306:3306 agustinoria/ministerio_produccion

Container con volumen:
docker run -d -p 80:80 -p 3306:3306 -v "/directorio_de_preferencia:/app" agustinoria/ministerio_produccion

• PASO 3: Cargar un set de datos en la base de datos:

chmod +x ministerio_produccion_database_configuration.sh
./ministerio_produccion_database_configuration.sh