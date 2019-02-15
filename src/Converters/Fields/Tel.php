<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Tel extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        if (class_exists('\libphonenumber\PhoneNumberUtil')) {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

            try {
                $number = $field->toString();
                $proto = $phoneUtil->parse($number);

                $this->addAttribute('E164', $phoneUtil->format($proto, \libphonenumber\PhoneNumberFormat::E164));
                $this->addAttribute('national', $phoneUtil->format($proto, \libphonenumber\PhoneNumberFormat::NATIONAL));
                $this->addAttribute('international', $phoneUtil->format($proto, \libphonenumber\PhoneNumberFormat::INTERNATIONAL));
                $this->addAttribute('RFC3966', $phoneUtil->format($proto, \libphonenumber\PhoneNumberFormat::RFC3966));

            } catch (\libphonenumber\NumberParseException $e) {
                $this->addAttribute('error', $e->getMessage());
            }
        }

        $this->addAttribute('slug', preg_replace('/\s+/', '', $field->toString()));
        $this->root->nodeValue = $this->sanitize($field->toString());
    }
}
