<?php 
/**
 * ===============================================================
 * LessForDle - модуль для связки класса phpless с CMS DataLife Engine (8.x - 9.x). 
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
 * Версия: 1.1.0 (27.04.2013)
 * ===============================================================
 * 
 * Использование: 
 * ---------------------------------------------------------------
 * 
 * В начале main.tpl прописать {include file="engine/modules/less/less.php"}
 * По умолчанию подключается файл main.less из папки css текущего шаблона сайта
 * туда же записывается одноимённый css-файл 
 * Для указания собственных файлов и показа времени выполнения скрипта и отключения компесии css пишем примерно так:
 * {include file="engine/modules/less/less.php?&inputFile=/styles/file.less&outputFile=/css/style.css&showstat=y&nocompress=y"}
 * 
 */

// Как всегда стандартная строка 
if(!defined('DATALIFEENGINE') || $config['allow_comments'] != 'yes') {die('Hacking attempt!');}

	/**
	 * Переменные строки подключения:
	 * inputFile - входной файл .less
	 * outputFile - итоговый css-файл (по умолчанию имеет то же имя, что и исходный).
	 * showstat - показывать время работы модуля (показывается только для админа).
	 * nocompress - отключает сжатие css-файла.
	 */
	if(!is_string($inputFile)) $inputFile = '/css/main.less';
	if(!is_string($outputFile)) $outputFile = str_ireplace('.less', '.css', $inputFile);

	if($showstat && $member_id['user_id'] == 1) {
		$start = microtime(true);
	}
	
	/**
	 * Функция автокомпиляции less, запускается даже если изменён импортированный файл - очень удобно.
	 * функция взята из документации к классу.
	 * @param string $inpFile - входной файл (в котором могут быть и импортированные файлы)
	 * @param string $outFile - выходной файл
	 * @return file
	 */
	function autoCompileLess($inpFile, $outFile) {

		$cacheFile = $inpFile.".cache";

		if (file_exists($cacheFile)) {
			$cache = unserialize(file_get_contents($cacheFile));
		} else {
			$cache = $inpFile;
		}

		// Подключаем класс для компиляции less 
		require "lessphp.class.php";
		$less = new lessc;
		if (!$nocompress) {
			$less->setFormatter('compressed');
		}
		
		$newCache = $less->cachedCompile($cache);

		if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($outFile, $newCache['compiled']);
		}
	}

	// Выполняем функцию компиляции
	try {
		autoCompileLess(TEMPLATE_DIR.$inputFile, TEMPLATE_DIR.$outputFile);
	} catch (exception $e) {
		// Если что-то пошло не так - скажем об этом пользователю.
		echo '<div style="text-align: center; background: #fff; color: red; padding: 5px;">LessForDle error: '.$e->getMessage().'</div>';
	}

	// Если разрешен показ времени выполнения - покажем его.
	if($showstat && $member_id['user_id'] == 1) {
		echo '<div style="text-align: center; background: #fff; color: red; padding: 5px;">LessForDle complete in: <b>'. round((microtime(true) - $start), 6). '</b> sec.</div>';
	}

?>