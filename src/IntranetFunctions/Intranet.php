<?php
namespace Bureau\Framework\IntranetFunctions {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;

    /**
     * Class Intranet
     * @package BureauFramework
     */
    class Intranet
    {
        /**
         * Функция для получения начальника подразделения по id сотрудника
         *
         * @param $user_id
         * @return array
         */
        function getBitrixUserManager($user_id)
        {
            $managers = array();
            if (Loader::IncludeModule('intranet')) {
                $sections = CIntranetUtils::GetUserDepartments($user_id);
                foreach ($sections as $section) {
                    $manager = CIntranetUtils::GetDepartmentManagerID($section);
                    while (empty($manager)) {
                        $res = CIBlockSection::GetByID($section);
                        if ($sectionInfo = $res->GetNext()) {
                            $manager = CIntranetUtils::GetDepartmentManagerID($section);
                            $section = $sectionInfo['IBLOCK_SECTION_ID'];
                            if ($section < 1) break;
                        } else break;
                    }
                    If ($manager > 0) $managers[] = $manager;
                }
                return $managers;
            } else {
                return false;
            }
        }
    }
}