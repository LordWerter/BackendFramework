<?php
namespace Bureau\Framework\Debugging {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    /**
     * Class Debug
     * @package BureauFramework
     */
    class Debug
    {
        /**
         * Функция для дебага
         *
         * @param $var объект трассировки
         * @param bool|false $vardump использовать var_dump вместо print_r
         * @param bool|false $return вернуть результат вместо вывода на экран
         * @return string - содержимое объекта
         */

        function dump($var, $vardump = false, $return = false)
        {
            static $dumpCnt;

            if (is_null($dumpCnt)) {
                $dumpCnt = 0;
            }
            ob_start();

            echo '<p>';
            $style = "
            border: 1px solid #696969;
            background: #eee;
            border-radius: 3px;
            font-size: 14px;
            font-family: calibri, arial, sans-serif;
            padding: 20px; color: #000;
            ";
            echo '<b>DUMP #' . $dumpCnt . ':</b> ';
            echo '<pre style="' . $style . '">';
            if ($vardump) {
                var_dump($var);
            } else {
                print_r($var);
            }
            echo '</pre>';
            echo '</p>';

            $cnt = ob_get_contents();
            ob_end_clean();
            $dumpCnt++;
            if ($return) {
                return $cnt;
            } else {
                echo $cnt;
            }
        }

    }
}