echo 'restore database  varcave4 with previous delete'

if [ ! -f "$1" ];then
  echo ERROR please use sqlfile as 1st argument
  echo '  Usage : ./$0 "sql_file.sql" '
  exit 1
fi

mysql -P 3306 -uroot -p -e "SET GLOBAL log_bin_trust_function_creators = 1;"
mysql -P 3306 -uroot -p -e "DROP DATABASE varcave4; create database varcave4"
echo "exec : mysql -P 3306 -uroot -p varcave4 < \'$1\'"
mysql -P 3306 -uroot -p varcave4 < "$1"