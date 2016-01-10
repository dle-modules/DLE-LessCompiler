<?php
/*
=============================================================================
DLE-LessCompiler — LESS-Компилятор для DLE
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
Версия: 3.1.0 (10.01.2016)
=============================================================================
 */

/**
 * @global array $member_id
 * @global array $config
 */

if (!defined('DATALIFEENGINE')) {
	die("Go fuck yourself!");
}

// Для уеньшения нагрузки и возможности использовать в реальных пректах выполняем код только если авторизованы как группа админов
if ($member_id['user_group'] != 1) {
	return;
}

// Путь к папке от корня сайта (конечную папку less указывать не нужно)
$localSpaceFolder = !empty($folder) ? $folder : '/templates/' . $config['skin'];

// Названия less файлов, которые нужно скомпилить.
$files = !empty($files) ? $files : 'main';
// Путь относительно localSpaceFolder куда будет помещен скомпилированный css-файл.
$outputPath = !empty($outputPath) ? $outputPath : '/css/';
// Минифицировать css файл
$compress = !isset($compress) ? false : true;
// Генерировать  sourceMap
$sourceMap = !isset($sourceMap) ? false : true;

// Путь к корню сайта
$rootFolder = ROOT_DIR;

// Превращаем имена файлов в массив
$fileNames = explode(',', $files);

// Подключаем класс-обёртку компилятора
require_once 'compile.php';

// Компилим в соответсвии с параметрами
$compile = new dleLessCompiler($rootFolder, $localSpaceFolder, $fileNames, $outputPath, $compress, $sourceMap);
// На выходе получаем массив
$file = $compile->compile();

// Если в массиве есть ошибка - значит компиляция не прошла, и нужно об этом сообщить
if ($file['error']) {
	$rootFolder = str_replace('\\', '/', $rootFolder);
	$errorStyle = '<link rel="stylesheet" href="/templates/' . $config['skin'] . '/css/-less-error.css">';
	$errorText  = '<div class="less-error-wrapper"><div class="less-error-content"><div class="less-error"><div class="less-error-header">DLE-LessCompiler Error!</div>' . str_replace(($rootFolder), '', $file['error']) . '</div></div></div>';
	echo $errorStyle . $errorText;
}