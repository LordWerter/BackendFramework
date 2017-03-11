<?php
namespace Bureau\Framework\IBlocks {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    /**
     * Class IBlock
     * @package BureauFramework
     */
    class IBlock
    {
        /**
         * Функция для получения массива с данными о инфоблоке
         *
         * @param $IBlockID - ID инфоблока
         * @return $stack: array - массив данных об инфоблоке или false
         */
        public static function GetIBlock($IBlockCode) {
            if (Loader::IncludeModule('iblock')) {
                $res = \CIBlock::GetList(
                    array(),
                    array(
                        'CODE' => $IBlockCode,
                    ), true
                );
                while ($stack = $res->Fetch()) {
                    if (!empty($stack)) {
                        return $stack;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        /**
         *
         * Функция получения данных о разделе по ID
         *
         * @param $SectionID - ID раздела
         * @return array|bool
         */
        public static function GetIBlockSection($SectionID) {
            if (Loader::IncludeModule('iblock')) {
                $res = \CIBlockSection::GetList(
                    array(),
                    array(
                        'ID'        => $SectionID,
                    ), true
                );
                while ($stack = $res->Fetch()) {
                    if (!empty($stack)) {
                        return $stack;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

    }
}