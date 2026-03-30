cd /d %~dp0
"C:\webdev\MySQL Server\bin\mysql.exe" -P 3306 -uroot -ppasse -e "SET GLOBAL log_bin_trust_function_creators = 1;"
"C:\webdev\MySQL Server\bin\mysql.exe" -P 3306 -uroot -ppasse -e "DROP DATABASE varcave4; create database varcave4"
"C:\webdev\MySQL Server\bin\mysql.exe" -P 3306 -uroot -ppasse varcave4 < .\speleocdqjvarcav-2026-03-27.sql