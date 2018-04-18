<?php

use \Nuxia\BusinessDayManipulator\Manipulator;

return [

    /*
     * Days that are repeated every year.
     */
    'holidays' => [
        new \DateTime("2018-12-25"),
        new \DateTime('2018-01-01'),
        new \DateTime('2018-04-25'),
        new \DateTime('2018-05-01'),
    ],

    /*
     * Days that are not repeated.
     */
    'freeDays' => [
        new \DateTime('2018-03-30'),
    ],

    /*
     * Days that are repeated every week.
     */
    'freeWeekDays' => [
        Manipulator::SATURDAY,
        Manipulator::SUNDAY,
    ],
];