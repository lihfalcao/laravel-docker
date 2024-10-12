<?php
/* Configurações do phpMyAdmin */
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = 'db'; // O nome do serviço do MySQL no Docker
$cfg['Servers'][$i]['port'] = '3306'; // Porta do MySQL
$cfg['Servers'][$i]['user'] = 'root'; // Usuário do MySQL
$cfg['Servers'][$i]['password'] = 'root'; // Senha do MySQL
$cfg['Servers'][$i]['AllowNoPassword'] = false;
