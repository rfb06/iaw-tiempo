Weather App
Aplicación web de consulta meteorológica desarrollada en PHP con patrón MVC, desplegada con Docker sobre Apache.

Requisitos

Docker
Docker Compose
Clave de API de OpenWeatherMap


Instalación
# Editar .env y añadir la clave de API
docker compose up -d --build
La aplicación queda disponible en http://100.53.171.4:80.

Configuración
VariableDescripciónOPENWEATHER_API_KEYClave de API de OpenWeatherMap
El resto de parámetros se encuentran en config/config.php:
ConstanteValor por defectoDescripciónAPP_BASE''Prefijo de ruta si la app no está en la raízCACHE_TTL600Segundos que se cachean las respuestas de la APICACHE_DIRcache/Carpeta donde se guardan los ficheros de caché

Rutas
MétodoRutaDescripciónGET/Página de inicio con formulario de búsquedaPOST/searchGeocodifica la ciudad y redirige a /cityGET/city?name=&country=&lat=&lon=Muestra toda la información meteorológica

Funcionalidades

Búsqueda de ciudad con geocodificación (lat/lon) vía Geocoding API
Tiempo actual: temperatura, sensación térmica, viento, humedad, presión, visibilidad, nubosidad, amanecer y atardecer
Previsión por horas del día actual en franjas de 3 horas
Previsión semanal con máximas, mínimas y probabilidad de lluvia para 5 días
Historial de las últimas 5 búsquedas guardado en sesión PHP
Caché de respuestas en disco para reducir llamadas a la API


Despliegue en AWS EC2 (Debian)
bash# 1. Instalar Docker
sudo apt update && sudo apt install -y ca-certificates curl gnupg
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian bookworm stable" | sudo tee /etc/apt/sources.list.d/docker.list
sudo apt update && sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
sudo usermod -aG docker $USER && exit  # volver a entrar

# 2. Subir el proyecto desde local
scp -i vockey.pem apptiempo.zip admin@TU_IP:~

# 3. Desplegar
ssh -i cockey.pem admin@TU_IP
unzip apptiempo.zip && cd apptiempo
echo "OPENWEATHER_API_KEY=tu_clave" > .env
docker compose up -d --build
Abrir el puerto 80 en el Security Group de EC2 (HTTP, origen 0.0.0.0/0).

Actualizar en producción
bashscp -i vockey.pem apptiempo.zip admin@TU_IP:~
ssh -i vockey.pem admin@TU_IP
unzip ~/apptiempo.zip
cd ~/apptiempo && docker compose down && docker compose up -d --build

Comandos útiles
bashdocker compose logs -f                                              # Ver logs
docker compose down                                                 # Parar
docker compose exec weather-app rm -f cache/*.json                 # Limpiar caché
docker compose exec weather-app printenv OPENW
# El docker desplegado se inicia automaticamente y puedes acceder desde eltiemporicardo.ddns.net
