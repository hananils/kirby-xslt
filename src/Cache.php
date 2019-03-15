<?php

namespace Hananils;

use DomDocument;

class Cache
{
    public static function get($page, $included = null)
    {
        $id = self::generatePageId($page, $included);
        $key = md5(json_encode($included));
        $cache = kirby()->cache('hananils.xslt')->get($id);

        if ($cache) {
            if ($included === null) {
                return $cache;
            } elseif (isset($cache[$key])) {
                $cached = new DomDocument('1.0', 'utf-8');
                $cached->loadXML($cache[$key]['xml']);

                return $cached->documentElement;
            }
        }

        return null;
    }

    public static function set($page, $included, $xml)
    {
        $id = self::generatePageId($page, $included);
        $cache = kirby()->cache('hananils.xslt')->get($id);

        $key = md5(json_encode($included));
        $cache[$key] = [
            'included' => $included,
            'xml' => $xml
        ];

        kirby()->cache('hananils.xslt')->set($id, $cache, 60 * 24 * 365);

        self::setAssociation($page, $page);
    }

    public static function getAssociations($page)
    {
        $id = self::generateAssociationId($page);
        $associations = kirby()->cache('hananils.xslt')->get($id);

        if (empty($associations)) {
            $associations = [];
        }

        return $associations;
    }

    public static function setAssociation($page, $referrer)
    {
        $context = self::generateAssociationId($page);

        $associations = self::getAssociations($page);
        $associations[] = self::generatePageId($referrer);
        $associations = array_unique($associations);
        $associations = array_filter($associations);

        kirby()->cache('hananils.xslt')->set($context, $associations, 60 * 24 * 365);
    }

    public static function clear($page = null)
    {
        if ($page) {
            self::clearAssociations($page);
            kirby()->cache('hananils.xslt')->remove(self::generateAssociationId($page));
        } else {
            kirby()->cache('hananils.xslt')->flush();
            kirby()->cache('pages')->flush();
        }
    }

    public static function clearAssociations($page = null)
    {
        if (!$page) {
            return;
        }

        $associations = self::getAssociations($page);
        foreach ($associations as $association) {
            kirby()->cache('hananils.xslt')->remove($association);
        }

        kirby()->cache('hananils.xslt')->remove(self::generateAssociationId($page));
    }

    public static function generatePageId($page)
    {
        return 'pages/' . $page->id() . '/xml';
    }

    public static function generateAssociationId($page)
    {
        return 'pages/' . $page->id() . '/associations';
    }

}
