<?
namespace Bureau\Framework\WorkWithCache {

    /**
     * Class CacheUncache
     * @package BureauFramework
     */
    class CacheUncache
    {

        /**
         * @var int
         * Переменная, в которой хранится текущий номер вызываемой некешируемой области
         * Вероятно, не нужна, но необходимо тестировать
         */
        private static $count = 0;


        /**
         * Оставлено на случай подключения в административной части
         */
        static public function OnEpilogHandler()
        {
            global $APPLICATION;
            if (is_object($APPLICATION)) {
                if (self::CheckPath()) {
                    $APPLICATION->AddBufferContent(array(
                        __CLASS__,
                        'EmptyFunction'
                    ));
                }
            }
        }

        /**
         * Будущий обработчик для вложенных подключений
         */
        static public function OnBeforeEndBufferContentHandler()
        {
            global $APPLICATION;
            if (is_object($APPLICATION)) {
                if (self::CheckPath()) {
                    if (is_array($APPLICATION->buffer_content)) {
                        $depth = count($APPLICATION->buffer_content);
                        for ($i = 0; $i < $depth; $i++) {
                            if ($APPLICATION->buffer_content[$i] !== '') {
                                $APPLICATION->buffer_content[$i] = self::ReplaceAllIncludeAreaHtml($APPLICATION->buffer_content[$i]);
                            }
                        }
                    }
                }
            }
        }

        /**
         * @param $content
         * Обработчик событий, заменяющий закешированный контент на некешируемый
         */
        static public function OnEndBufferContentHandler(&$content)
        {
            global $APPLICATION;
            if (is_object($APPLICATION)) {
                if (self::CheckPath()) {
                    $content = self::ReplaceAllIncludeAreaHtml($content);
                }
            }
        }

        /**
         * @return bool
         * Функция, проверяющяя не в админке ли мы и не смотрим ли файл оттуда. Нужность неизвестна, потому что родительская функция не дописана
         */
        static public function CheckPath()
        {
            $isAdminPath = false;
            global $APPLICATION;
            if (!defined('ADMIN_SECTION') || ADMIN_SECTION !== true) {
                $curDir = $APPLICATION->GetCurDir();
                if (substr($curDir, 0, 8) != '/bitrix/' || substr($curDir, 0, 18) == '/bitrix/templates/' || substr($curDir, 0, 19) == '/bitrix/components/') {
                    $isAdminPath = true;
                }
            }
            return $isAdminPath;
        }

        /**
         * @param $arParams
         * Главная функция. Обозначает начало некешируемой области. На данный момент ОБЯЗАНА располагаться на отдельной строке.
         */
        static public function IncludeStart($arParams)
        {
            self::$count++;
            $fileInfo = self::GetCalledFile();

            echo '<!--webprofy.uncache ' . self::$count . '|||' . base64_encode(serialize(array($fileInfo['file'], $fileInfo['line']))) . '|||' . base64_encode(serialize($arParams)) . ' ';
        }

        /**
         * Главная функция. Обозначает конец некешируемой области. На данный момент ОБЯЗАНА располагаться на отдельной строке.
         */
        static public function IncludeStop()
        {
            echo 'webprofy.uncache.end-->';
        }


        /**
         * @param $content
         * @return mixed
         * Замена всех найденных кешированных областей на некешируемые
         */
        static private function ReplaceAllIncludeAreaHtml($content)
        {
            $tempFileName = $_SERVER['DOCUMENT_ROOT'] . '/upload/custom_cache/uncache/temp.php';
            file_put_contents($tempFileName, $content);
            if (mb_strpos($content, '<!--webprofy.uncache') !== false) {
                $content = preg_replace_callback('/<!--webprofy.uncache[\s]+(.*?)[\s](.*?)webprofy.uncache.end-->/s', array(
                    __CLASS__,
                    'GetIncludeAreaHtml'
                ), $content);
            }
            return $content;
        }

        /**
         * @param $arFile
         * @return string
         * Получение хтмл для вставки
         */
        static private function GetIncludeAreaHtml($arFile)
        {
            $content = "";
            if (!defined('BX_BUFFER_SHUTDOWN')) {
                ob_start();
                self::IncludeArea($arFile);
                $content = ob_get_contents();
                ob_end_clean();
            }
            return $content;
        }

        /**
         * @param $arFile
         * Функция, непосредственно осуществляющая замену кешированной области на некешируемую. Вся магия происходит здесь
         */
        static private function IncludeArea($arFile)
        {
            global $APPLICATION;
            $paramsFull = $arFile[1];
            $cachedContent = $arFile[2];
            if (self::GetDebug() == "Y") {
                IncludeModuleLangFile(__FILE__);
                $debug = '<pre style="padding: 5px; background-color: #AAA;">' . htmlspecialchars($arFile[0]) . '

' . GetMessage('WEBPROFY_INCLUDE_array_title') . ':
' . htmlspecialchars(print_r($arFile, true)) . '</pre>';
                echo $debug;
            }
            $arParamsFull = explode('|||', $paramsFull);
            $fileId = $arParamsFull[0];
            $fileInfoEncoded = $arParamsFull[1];
            $fileInfo = unserialize(base64_decode($fileInfoEncoded));
            $fileParamsEncoded = $arParamsFull[2];
            $fileParams = unserialize(base64_decode($fileParamsEncoded));
            foreach ($fileParams as $paramKey => $paramVal) {
                $$paramKey = $paramVal;
            }
            $tempFileName = $_SERVER['DOCUMENT_ROOT'] . '/upload/custom_cache/uncache/' . md5($fileInfo[0] . $fileInfo[1]) . '.php';
            if (is_file($tempFileName) && (strtotime('-1 day') < filemtime($tempFileName))) {
                include $tempFileName;
            } else {
                unlink($tempFileName);
                $fileContent = file($fileInfo[0]);
                $arrayFrom = array_splice($fileContent, $fileInfo[1]);
                $stringToTempFile = '';
                foreach ($arrayFrom as $line) {
                    if (mb_strpos($line, 'UnCache::IncludeStop') === false) {
                        $stringToTempFile .= $line . PHP_EOL;
                    } else {
                        break;
                    }
                }
                if (!file_exists(dirname($tempFileName))) {
                    mkdir(dirname($tempFileName), 0775, true);
                }
                file_put_contents($tempFileName, $stringToTempFile);
                include $tempFileName;
            }
        }

        /**
         * Обработчик событий, устанавливающий дебаг. Пока не используется
         */
        static public function OnBeforePrologHandler()
        {
            global $APPLICATION;
            global $USER;
            if (isset($_REQUEST['WEBPROFY_INCLUDE_DEBUG'])) {
                if ($USER->IsAuthorized()) {
                    $rights = $APPLICATION->GetGroupRight('webprofy.uncache');
                    if ($rights >= 'R') {
                        self::SetDebug($_REQUEST['WEBPROFY_INCLUDE_DEBUG']);
                    }
                }
            }
        }

        /**
         * @return string
         * Функция, получающая текущий статус дебага. Пока не используется.
         */
        static private function GetDebug()
        {
            $debugMode = "N";
            if (isset($_SESSION['WEBPROFY_INCLUDE_DEBUG'])) {
                $debugMode = $_SESSION['WEBPROFY_INCLUDE_DEBUG'];
            }
            return $debugMode;
        }

        /**
         * @param $reqDebug
         * Функция, устанавливающая дебаг. Пока не используется
         */
        static private function SetDebug($reqDebug)
        {
            $debugValues = array(
                "N" => true,
                "Y" => true
            );
            if (isset($debugValues[$reqDebug])) {
                $_SESSION['WEBPROFY_INCLUDE_DEBUG'] = $reqDebug;
            }
        }

        /**
         * @return mixed
         * Вспомогательная функция, необходимая для поиска файла и строки, в котором вызвана функция отмены кеширования
         */
        static public function GetCalledFile()
        {
            $bt = debug_backtrace();
            array_shift($bt);
            $caller = array_shift($bt);
            return $caller;
        }

        public static function IncludeComponent($componentName, $componentTemplate, $arParams = array(), $parentComponent = null, $arFunctionParams = array())
        {
            //self::IncludeStart($arParams);
        }

        /**
         *
         */
        static public function EmptyFunction()
        {
        }
    }
}