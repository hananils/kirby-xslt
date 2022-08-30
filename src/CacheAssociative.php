<?php

namespace Hananils;

use DomDocument;

class CacheAssociative
{
    public static $duration = 60 * 24 * 365;

    public static function get($context, $name, $options = [])
    {
        $id = self::generateId($context);
        $key = self::generateKey($name, $options);
        $cache = kirby()
            ->cache('hananils.kirby-xslt')
            ->get($id);

        if ($cache) {
            if ($options === null) {
                return $cache;
            } elseif (isset($cache[$key])) {
                $cached = new DomDocument('1.0', 'utf-8');
                $cached->loadXML($cache[$key]['xml']);

                return $cached->documentElement;
            }
        }

        return null;
    }

    public static function set($context, $xml, $name, $options = [])
    {
        $id = self::generateId($context);
        $cache = kirby()
            ->cache('hananils.kirby-xslt')
            ->get($id);

        $key = self::generateKey($name, $options);
        $cache[$key] = [
            'name' => $name,
            'options' => $options,
            'xml' => $xml
        ];

        if (kirby()->multilang()) {
            $cache[$key]['language'] = kirby()
                ->language()
                ->code();
        }

        kirby()
            ->cache('hananils.kirby-xslt')
            ->set($id, $cache, self::$duration);

        self::setAssociation($context, $context);
    }

    public static function getAssociations($context)
    {
        $id = self::generateId($context, 'associations');
        $associations = kirby()
            ->cache('hananils.kirby-xslt')
            ->get($id);

        if (empty($associations)) {
            $associations = [];
        }

        return $associations;
    }

    public static function setAssociation($context, $referrer)
    {
        $id = self::generateId($context, 'associations');

        $associations = self::getAssociations($context);
        $associations[] = self::generateId($referrer);
        $associations = array_unique($associations);
        $associations = array_filter($associations);

        kirby()
            ->cache('hananils.kirby-xslt')
            ->set($id, $associations, self::$duration);
    }

    public static function clear($context = null)
    {
        if ($context) {
            self::clearAssociations($context);
            kirby()
                ->cache('hananils.kirby-xslt')
                ->remove(self::generateId($context));
        } else {
            kirby()
                ->cache('hananils.kirby-xslt')
                ->flush();
            kirby()
                ->cache('pages')
                ->flush();
        }
    }

    public static function clearAssociations($context = null)
    {
        if (!$context) {
            return;
        }

        $associations = self::getAssociations($context);
        foreach ($associations as $association) {
            kirby()
                ->cache('hananils.kirby-xslt')
                ->remove($association);
        }

        kirby()
            ->cache('hananils.kirby-xslt')
            ->remove(self::generateId($context, 'associations'));
    }

    public static function generateKey($name = 'page', $options = [])
    {
        if (kirby()->multilang()) {
            $options['language'] = kirby()
                ->language()
                ->code();
        }

        return md5($name . json_encode($options));
    }

    public static function generateId($context, $type = 'xml')
    {
        return 'content/' . $context->id() . '/' . $type;
    }
}
