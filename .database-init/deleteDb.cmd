cd /d %~dp0
"c:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -P 3306 -uroot -ppasse -e "SET GLOBAL log_bin_trust_function_creators = 1;"
"c:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -P 3306 -uroot -ppasse -e "DROP DATABASE varcave4; create database varcave4"
"c:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -P 3306 -uroot -ppasse varcave4 < .\speleocdqjvarcav-2025-12-30.sql