<?php
namespace Bureau\Framework\HLBlocks {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    class HLBlock
    {
        /**
         * Функция для получения массива элементов из справочника (Highload-блока)
         *
         * @param $HighloadTable - Название таблицы Highload-блока
         * @param $NeededFields - Массив из символьных кодов полей, которые надо получить для каждого элемента справочника
         * @return array - Массив элементов справочника
         */

        function GetHighloadElems($HighloadTable, $NeededFields = array("ID", "UF_XML_ID", "UF_NAME"))
        {
            $stack = array();
            if (Loader::IncludeModule('highloadblock')) {
                $rsData = HL\HighloadBlockTable::getList(
                    array(
                        'filter' => array(
                            'TABLE_NAME' => $HighloadTable,
                        )
                    )
                );
                if ($arData = $rsData->fetch()) {
                    $hlblock = HL\HighloadBlockTable::getById($arData["ID"])->fetch();  // получаем объект вашего HL блока
                    $entity = HL\HighloadBlockTable::compileEntity($hlblock);           // получаем рабочую сущность
                    $entity_data_class = $entity->getDataClass();
                    $rsData = $entity_data_class::getList(array(
                        'select' => $NeededFields
                    ));
                    while ($element = $rsData->fetch()) {
                        array_push($stack, $element);
                    }
                    return $stack;
                } else {
                    return false;
                }
            }
        }

        /**
         * Функция проверяет наличие HL-блока в БД
         * @param $HLBlockTableName - название таблицы HL-блока в БД
         * @return bool
         */
        function HLBlockInspection($HLBlockTableName)
        {
            if (CModule::IncludeModule('highloadblock')) {
                $rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                    array(
                        'filter' => array(
                            'TABLE_NAME' => $HLBlockTableName,
                        )
                    )
                );

                if (!($arData = $rsData->fetch())) {
                    return true;
                } else {
                    return false;
                }
            }
        }

    }
}