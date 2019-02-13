<?php

namespace Hananils\Converters;

use DateTime;
use Hananils\Xml;
use IntlDateFormatter;

class Dates extends Xml {
    public function parse($datetime = 'now') {
        $date = new DateTime($datetime);

        // Today
        $this->addElement('today', $date->format('Y-m-d'), [
            'iso' => $date->format('c'),
            'year' => $date->format('Y'),
            'month' => $date->format('n'),
            'day' => $date->format('j'),
            'timestamp' => $date->format('U'),
            'time' => $date->format('H:i'),
            'weekday' => $date->format('N'),
            'offset' => $date->format('O')
        ]);

        // Language codes
        $codes = [];
        if (kirby()->languages()->count()) {
            $codes = kirby()->languages()->codes();
        } else {
            $codes[] = kirby()->option('locale');
        }

        if (empty($codes)) {
            $this->addElement('language', null, [
                'error' => 'Neither locale nor languages defined.'
            ]);

            return;
        }

        // Date
        $date = new DateTime('1st January');
        $timestamps = [];

        // Months
        $timestamps['months'] = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamps['months'][] = $date->getTimestamp();
            $date->modify('+1 month');
        }

        // Weekdays
        $timestamps['weekdays'] = [];
        $date->modify('last Sunday');
        for ($i = 1; $i <= 7; $i++) {
            $timestamps['weekdays'][] = $date->getTimestamp();
            $date->modify('+1 day');
        }

        // Loop through languages
        foreach ($codes as $code) {
            setlocale(LC_TIME, $code);

            // Language strings
            $format = new IntlDateFormatter($code, IntlDateFormatter::FULL, IntlDateFormatter::FULL);
            $locale = explode('.', strtolower($code));
            $language = $this->addElement('language', null, [
                'id' => $format->getLocale(),
                'locale' => $locale[0]
            ]);

            // Generate months
            $count = 1;
            $months = $this->addElement('months', null, null, $language);
            foreach ($timestamps['months'] as $month) {
                $this->addElement('month', strftime('%B', $month), [
                    'id' => $count++,
                    'abbr' => strftime('%b', $month)
                ], $months);
            }

            // Generate weekdays
            $count = 1;
            $weekdays = $this->addElement('weekdays', null, null, $language);
            foreach ($timestamps['weekdays'] as $weekday) {
                $this->addElement('weekday', strftime('%A', $weekday), [
                    'id' => $count++,
                    'abbr' => strftime('%a', $weekday)
                ], $weekdays);
            }
        }
    }
}
