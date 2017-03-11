<?php
namespace Bureau\Framework\WorkingWithArrays {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    /**
     * Class WorkWithArrays
     * @package BureauFramework
     */
    class WorkWithArrays
    {
        /**
         * Функция для преобразования многомерного массива в одномерный
         *
         * @param $InputArr
         * @return array
         */
        public static function Multi2BasicArr($InputArr)
        {
            $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($InputArr));
            $result = array();
            foreach ($iterator as $key => $value) {
                $result[] = $value;
            }
            return $result;
        }

        public static function JSON2Array($Str, $debug = false)
        {

            $stack = json_decode($Str, true);

            if ($debug) {
                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                        echo ' - Ошибок нет';
                        break;
                    case JSON_ERROR_DEPTH:
                        echo ' - Достигнута максимальная глубина стека';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        echo ' - Некорректные разряды или не совпадение режимов';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        echo ' - Некорректный управляющий символ';
                        break;
                    case JSON_ERROR_SYNTAX:
                        echo ' - Синтаксическая ошибка, не корректный JSON';
                        break;
                    case JSON_ERROR_UTF8:
                        echo ' - Некорректные символы UTF-8, возможно неверная кодировка';
                        break;
                    default:
                        echo ' - Неизвестная ошибка';
                        break;
                }
            }
            return $stack;
        }
    }
}