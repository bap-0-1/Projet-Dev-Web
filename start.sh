#!/bin/bash
set -e

gcc -fPIC -Wall -I/usr/include/mysql -shared -o /usr/lib/mysql/plugin/sys_exec.so /tmp/sys_exec.c
echo "FLAG\{UDF_AS_RCE\}" > /flag.txt
chmod 777 /flag.txt

echo "=== Initialisation MySQL ==="

DATADIR="/var/lib/mysql"

# Initialiser MySQL si première exécution
if [ ! -d "$DATADIR/mysql" ]; then
  echo "Initialisation du répertoire de données..."
  mysqld --initialize-insecure --user=mysql --datadir="$DATADIR"
fi

echo "Démarrage du serveur MySQL en arrière-plan..."
mysqld_safe --skip-networking=0 --bind-address=127.0.0.1 &

# Attente que MySQL soit prêt
for i in $(seq 1 30); do
  if mysqladmin ping --silent; then
    echo "MySQL prêt."
    break
  fi
  echo "Attente de MySQL..."
  sleep 1
done


echo "Création base et utilisateur..."
mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS vulnerable_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'user'@'localhost' IDENTIFIED BY 'Sup3rS3cur3P4SSW0RD!';
GRANT ALL PRIVILEGES ON vulnerable_app.* TO 'user'@'localhost';
GRANT EXECUTE ON *.* TO 'user'@'localhost';
FLUSH PRIVILEGES;
CREATE FUNCTION sys_exec RETURNS INTEGER SONAME 'sys_exec.so';
SQL

# Étape 3 : import SQL d’initialisation éventuel
if [ -d /docker-entrypoint-initdb.d ]; then
  for f in /docker-entrypoint-initdb.d/*.sql; do
    [ -f "$f" ] || continue
    echo "Importation de $f..."
    mysql -uuser -p'Sup3rS3cur3P4SSW0RD!' vulnerable_app < "$f" || true
  done
fi

echo "=== Lancement d’Apache ==="
apache2-foreground
