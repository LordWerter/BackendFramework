<?php
namespace Bureau\Framework\StringProcessing {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    /**
     * Class StringProcess
     * @package BureauFramework
     */
    class StringProcess
    {

        /**
         * @param $string - строка на русском языке, которую необходимо перевести в транслит
         * @return string - строка в траслите
         */

        public static function rus2translit($string)
        {
            $converter = array(
                'а' => 'a', 'б' => 'b', 'в' => 'v',
                'г' => 'g', 'д' => 'd', 'е' => 'e',
                'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
                'и' => 'i', 'й' => 'y', 'к' => 'k',
                'л' => 'l', 'м' => 'm', 'н' => 'n',
                'о' => 'o', 'п' => 'p', 'р' => 'r',
                'с' => 's', 'т' => 't', 'у' => 'u',
                'ф' => 'f', 'х' => 'h', 'ц' => 'c',
                'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
                'ь' => '\'', 'ы' => 'y', 'ъ' => '\'',
                'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

                'А' => 'A', 'Б' => 'B', 'В' => 'V',
                'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
                'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
                'И' => 'I', 'Й' => 'Y', 'К' => 'K',
                'Л' => 'L', 'М' => 'M', 'Н' => 'N',
                'О' => 'O', 'П' => 'P', 'Р' => 'R',
                'С' => 'S', 'Т' => 'T', 'У' => 'U',
                'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
                'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
                'Ь' => '\'', 'Ы' => 'Y', 'Ъ' => '\'',
                'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            );
            return strtr($string, $converter);
        }


        /**
         * @param $string - строка на вход для перевода в символьным кодом(ЧПУ-ссыль)
         * @return $str|string - строка, кот. будет символьным кодом элемента
         */

        public static function str2url($string)
        {
            // в нижний регистр
            $str = strtolower($string);
            // переводим в транслит
            $str = rus2translit($str);
            // заменям все ненужное нам на "-"
            $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
            // удаляем начальные и конечные '-'
            $str = trim($str, "-");
            return $str;
        }
    }
}