<?php
namespace Bureau\Framework\Debugging {

    use Bitrix\Main\Loader;
    use \Bitrix\Highloadblock as HL;
    use Bitrix\Main\Mail\Event;

    /**
     * Class Send
     * @package BureauFramework
     */
    class Send
    {
        /**
         * @param $EventName
         * @param $SiteID
         * @param $arPostFields
         * @return int
         */
        public static function SendMsg($EventName, $SiteID, $arPostFields) {
            $ourEvent = Event::send(array(
                "EVENT_NAME" => $EventName,
                "LID" => $SiteID,
                "C_FIELDS" => $arPostFields,
            ));
            $EventID = $ourEvent->getId();
            return $EventID;
        }
    }
}