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
 */

require_once 'Less/Autoloader.php';

Less_Autoloader::register();

/**
 *
 */
class dleLessCompiler {

	public $rootFolder       = false;
	public $localSpaceFolder = '/templates/Default/less/';
	public $fileNames        = array('bootstrap');
	public $compress         = false;
	public $sourceMap        = false;

	/**
	 * @param $rootFolder
	 * @param $localSpaceFolder
	 * @param $fileNames
	 * @param $outputPath
	 * @param $compress
	 * @param $sourceMap
	 */
	function __construct($rootFolder, $localSpaceFolder, $fileNames, $outputPath, $compress, $sourceMap) {

		$lessConfig                   = new stdClass();
		$lessConfig->rootFolder       = $rootFolder;
		$lessConfig->localSpaceFolder = $localSpaceFolder;
		$lessConfig->fileNames        = $fileNames;
		$lessConfig->outputPath       = $outputPath;
		$lessConfig->compress         = $compress;
		$lessConfig->sourceMap        = $sourceMap;

		$this->config = $lessConfig;
	}

	/**
	 * @param $lessFiles
	 *
	 * @return array
	 */
	public function getFileList($lessFiles) {
		$arFiles = array();
		foreach ($lessFiles as $key => $lessFile) {
			$arFiles[$this->config->rootFolder . $this->config->localSpaceFolder . '/less/' . $lessFile . '.less'] = $this->config->rootFolder . $this->config->localSpaceFolder . $this->config->outputPath;
		}

		return $arFiles;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function compile() {

		$lessFiles = $this->getFileList($this->config->fileNames);

		try {
			$filePath = Less_Cache::Get($lessFiles, $this->setOptions());
			$error    = false;
		} catch (Exception $e) {
			$filePath = false;
			$error    = $e->getMessage();
		}

		$arReturn = array(
			'filePath' => $filePath,
			'error'    => $error,
		);

		return $arReturn;
	}

	/**
	 * @return array
	 */
	public function setOptions() {
		$arOptions                      = array();
		$arOptions['cache_dir']         = $this->config->rootFolder . $this->config->localSpaceFolder . '/less_cache';
		$arOptions['compress']          = $this->config->compress;
		$arOptions['sourceMap']         = $this->config->sourceMap;
		$arOptions['sourceMapWriteTo']  = $this->config->rootFolder . $this->config->localSpaceFolder . $this->config->outputPath . $this->config->fileNames[0] . '.map';
		$arOptions['sourceMapURL']      = $this->config->localSpaceFolder . $this->config->outputPath . $this->config->fileNames[0] . '.map';
		$arOptions['sourceRoot']        = '/';
		$arOptions['sourceMapBasepath'] = $this->config->rootFolder;
		$arOptions['output']            = $this->config->rootFolder . $this->config->localSpaceFolder . $this->config->outputPath . $this->config->fileNames[0] . '.css';
		$arOptions['relativeUrls']      = false;

		return $arOptions;
	}

}
