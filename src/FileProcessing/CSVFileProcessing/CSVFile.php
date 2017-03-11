<?php
namespace Bureau\Framework\FileProcessing\CSVFileProcessing {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    /**
     * Class CSVFile
     * @package BureauFramework
     */
    class CSVFile extends \Bureau\Framework\FileProcessing\FileProcess
    {
        /**
         * Функция для работы с CSV
         * @param $filePath - путь к файлу
         * @param $StrID - столбец, из которого надо считать данные
         * @return array - массив значений
         */
        public static function GetCSVVal($filePath, $StrID)
        {
            $res = fopen($filePath, 'r');
            while ($arRow = fgetcsv($res, 0, '	')) {
                if (!empty($arRow[$StrID]))
                    $arrVal[] = $arRow[$StrID];
            }
            fclose($res);
            return $arrVal;
        }

    }
}