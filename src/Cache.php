<?php

namespace Hananils;

use DomDocument;

class Cache
{
    private static $duration = 60 * 24 * 365;

    public static function get($type, $name)
    {
        $id = self::generateId($type, $name);
        $cache = kirby()->cache('hananils.xslt')->get($id);

        if ($cache) {
            $cached = new DomDocument('1.0', 'utf-8');
            $cached->loadXML($cache);

            return $cached->documentElement;
        }

        return null;
    }

    public static function set($xml, $type, $name)
    {
        $id = self::generateId($type, $name);
        kirby()->cache('hananils.xslt')->set($id, $xml, self::$duration);
    }

    public static function clear($type = null, $name = null)
    {
        if ($type && $name) {
            $id = self::generateId($type, $name);
            kirby()->cache('hananils.xslt')->remove($id);
        } else {
            kirby()->cache('hananils.xslt')->flush();
            kirby()->cache('pages')->flush();
        }
    }

    public static function generateId($type, $name)
    {
        return $type . '/' . $name;
    }

}
