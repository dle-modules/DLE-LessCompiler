<?php 
// Как всегда стандартная строка 
if(!defined('DATALIFEENGINE')) die('Hacking attempt!');

// Для уеньшения нагрузки и возможности использовать в реальных пректах выполняем код только если авторизованы как группа админов
if($member_id['user_group'] != 1) {
	return;
}

/**
 * ===============================================================
 * LessForDle - модуль для связки класса phpless с CMS DataLife Engine (8.x - 10.x). 
 * Модуль писался для своих нужд и для удобства разработки.
 * ===============================================================
 * 
 * Автор модуля: ПафНутиЙ 
 * URL: http://pafnuty.name/
 * ICQ: 817233 
 * email: pafnuty10@gmail.com
 * 
 * ===============================================================
 * Файл: less.php
 * ---------------------------------------------------------------
 * Версия: 2.0.0 (13.07.2013)
 * ===============================================================
 * 
 * Использование: 
 * ---------------------------------------------------------------
 * 
 * В начале main.tpl прописать {include file="engine/modules/less/less.php"}
 * По умолчанию подключается файл main.less из папки текущего шаблона сайта
 * туда же записывается одноимённый css-файл 
 * Полная строка подключения выглядит вот так:
 * {include file="engine/modules/less/less.php?lessLog=y&lessFileSize=25&lessLogFile=logfile&inputFile=/css/file1.less&outputFile=/css1/styles.css&normal=y&alertError=y"}
 *  
 * Настройки компиляции, они же и возможности ))
 * переменные строки подключения с параметрами:
 * 
 * &lessLog=y						// Вести лог-файл с отображением времени выполнения компиляции.
 * &lessFileSize=25					// Максимальный размер файла лога, в килобайтах (если размер файла будет больше, он удалится).
 * &lessLogFile=logfile				// Имя лог-файла. Файл является html-страницей и записывается в корень сайта.
 * &inputFile=/css/file1.less		// Входящий LESS-файл
 * &outputFile=/css1/styles.css		// Итоговый CSS-файл
 * &normal=y						// Отключение сжатия выходящего файла.
 * &alertError=y					// Показ ошибок компиляции через js-alert (иногда так удобнее, чем вверху страницы)
 * 
 * значения по умолчанию можно увидеть ниже 
 */

$lessLog		= !empty($lessLog)		? true : false;
$lessFileSize	= !empty($lessFileSize)	? $lessFileSize : '15';
$lessLogFile	= !empty($lessLogFile)	? $lessLogFile : 'less-log';

$inputFile		= !empty($inputFile)	? TEMPLATE_DIR.$inputFile : TEMPLATE_DIR."/main.less";
$outputFile		= !empty($outputFile)	? TEMPLATE_DIR.$outputFile : str_ireplace('.less', '.css', $inputFile);
$normal			= !empty($lessLog)		? true : false;
$alertError		= !empty($lessLog) 		? true : false;

/**
 * Конец настроек
 */

// Если включено логирование - "запускаем счётчик времени".
if($lessLog) {
	$timeStart = microtime(true);
	$logError = '';
}


// Выполняем функцию компиляции
try {
	autoCompileLess($inputFile, $outputFile, $normal);
} catch (exception $e) {
	// Если что-то пошло не так - скажем об этом пользователю способом, указанным в настройках и запишем в лог.
	$logError = str_replace($_SERVER['DOCUMENT_ROOT'], '', $e->getMessage());
	$showError = ($alertError) ? '<script>alert("Less error: '.$logError.'")</script>' : '<div style="text-align: center; background: #fff; color: red; padding: 5px;">Less error: '.$logError.'</div>';

	echo $showError;


}

// Если разрешено, то пишем лог-файл с временем выполнения компиляции less-файлов :)
if($lessLog) {
	$timeStop = microtime(true);
	$lessLog = round(($timeStop - $timeStart), 6);
	$textColor = ($lessLog > '0.01') ? 'red' : 'green';
	$mem_usg = '';
	$lessLogFile = $_SERVER['DOCUMENT_ROOT'].'/'.$lessLogFile.'.html';
	if(function_exists("memory_get_peak_usage")) $mem_usg = round(memory_get_peak_usage()/(1024*1024),2)."Мб";
	if ((file_exists($lessLogFile) && filesize($lessLogFile) > $lessFileSize*1024)) {
		unlink($lessLogFile);
	}
	if (!file_exists($lessLogFile)) {
			$cLessFile = fopen($lessLogFile, "wb");
			$firstText = "
				<!DOCTYPE html>
				<html lang='ru'>
				<head>
					<title>Лог времени выполнения компиляции LESS</title>
					<meta charset='".$config['charset']."'>
					<style>
						a {display: inline-block;margin-bottom: 5px;}
						.red {color: red;}
						.green {color: green;}
						table {margin: 50px auto; border-collapse: collapse;border: solid 1px #ccc; font: normal 14px Arial, Helvetica, sans-serif;}
						th b {cursor: help; color: #c00;}
						td {text-align: right;}
						th, td {font-size: 12px; border: solid 1px #ccc; padding: 5px 8px;}
						td:first-child {text-align: left;}
						tr:hover {background: #f0f0f0; color: #1d1d1d;}
					</style>
					<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
					<script>
						// Скрипт посчета среднего значения
						$.fn.getZnach = function (prop) {
							var options = $.extend({
								source: 'с',
								ins: '',
								quant: '5'
							}, prop);

							var summ = 0;
							this.each(function (i) {
								summ += +($(this).text().replace(/,/, '.').replace(options.source, ''));
							});
							$(options.ins).append('<br /><b title=\"Cреднее значение\">' + (summ / this.length).toFixed(options.quant) + options.source + '</b>');
						}
						// Инициализация скрипта
						jQuery(function ($) {
							$('td.timer').getZnach({
								ins: 'th.timer'
							});
							$('td.mem_usg').getZnach({
								source: 'Мб',
								ins: 'th.mem_usg',
								quant: '2'
							});
						});
					</script>
				</head>
				<body>
					<table class='stattable'>
						<tr>
							<th scope='col' class='queries'>Дата записи</th>
							<th scope='col' class='timer'>Вемя выполнения компилятора</th>
							<th scope='col' class='mem_usg'>Затраты памяти</th>
						</tr>
					\r\n</table></body></html>";
			fwrite($cLessFile, $firstText);
			fclose($cLessFile);

		} else {
			$cLessFileArr = file($lessLogFile);
			$lastLine = array_pop($cLessFileArr);
			$newText = implode("", $cLessFileArr);

			$newTextAdd = "добавляем строку, не спрашивайте, так надо!\r\n";
			if($logError) {
				$newTextAdd = "
					<tr>
						<td class='queries'>".date('Y-m-d H:i:s')."</td>
						<td colspan='2'><b class='red'>Ошибка: </b>".$logError."</td>
					</tr>\r\n";
			} else {
				$newTextAdd = "	
					<tr>
						<td class='queries'>".date('Y-m-d H:i:s')."</td>
						<td class='timer ".$textColor."'><b>".$lessLog."с</b></td>
						<td class='mem_usg'>".$mem_usg."</td>
					</tr>\r\n";
				
			}


			$cLessFile = fopen($lessLogFile, "w");	

			fwrite($cLessFile, $newText.$newTextAdd.$lastLine);
			fclose($cLessFile);
		}
	}

	/**
	 * Функция автокомпиляции less, запускается даже если изменён импортированный файл - очень удобно.
	 * функция взята из документации к классу и на просторах интернета.
	 * @param string $inpFile - входной файл (в котором могут быть и импортированные файлы)
	 * @param string $outFile - выходной файл
	 * @param string $nocompress - отключает сжатие выходного файла
	 * @return file
	 */
	function autoCompileLess($inpFile, $outFile, $nocompress = false) {

		$cacheFile = $inpFile.".cache";

		if (file_exists($cacheFile)) {
			$cache = unserialize(file_get_contents($cacheFile));
		} else {
			$cache = $inpFile;
		}

		// Подключаем класс для компиляции less 
		require "lessphp.class.php";
		$less = new lessc;
		if ($nocompress) {
			// Если запрещено сжатие - форматируем по нормальному с табами вместо пробелов.
			$formatter = new lessc_formatter_classic;
	        $formatter->indentChar = "\t";
	        $less->setFormatter($formatter);
		} else {
			// Иначе сжимаем всё в одну строку.
			$less->setFormatter('compressed');
		}
		
		$newCache = $less->cachedCompile($cache);

		if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($outFile, $newCache['compiled']);
		}
	}

?>
