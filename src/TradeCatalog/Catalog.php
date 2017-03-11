<?php
namespace Bureau\Framework\TradeCatalog {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;
    /**
     * Class Catalog
     * @package BureauFramework
     */
    class Catalog
    {
        /* Функция пробегается по arResult-у и для DISPLAY_PROPERTIES типа "Связь с Элементом" добавляет дополнительное свойство
        DISPLAY_VALUE_TEXT. Битрикс зачем-то по-умолчанию передаёт в DISPLAY_VALUE HTML-код с ссылкой */
        public static function displayPropertiesValueText(&$arResult){
            foreach ($arResult["ITEMS"] as &$arItem) {
                foreach ($arItem["DISPLAY_PROPERTIES"] as &$arProp) {
                    if($arProp["PROPERTY_TYPE"] == "E"){
                        $arProp["DISPLAY_VALUE_TEXT"] = strip_tags($arProp["DISPLAY_VALUE"]);
                    }
                }
            }
        }

    }
}