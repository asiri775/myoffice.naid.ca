<?php

namespace App\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;

class Constants
{

    const TOP_RATED_TOTAL_BOOKINGS = 50;
    const TOP_RATED_AVERAGE_RATING = 4.5;
    const DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    const PAYMENT_PAID = "paid";
    const PAYMENT_PARTIALLY_PAID = "partially-paid";
    const PAYMENT_FAILED = "failed";
    const PAYMENT_UNPAID = "unpaid";

    const BOOKING_STATUS_DRAFT = "draft";
    const BOOKING_STATUS_FAILED = "failed";
    const BOOKING_STATUS_SCHEDULED = "scheduled";
    const BOOKING_STATUS_BOOKED = "booked";
    const BOOKING_STATUS_CHECKED_IN = "checked-in";
    const BOOKING_STATUS_CHECKED_OUT = "checked-out";
    const BOOKING_STATUS_COMPLETED = "completed";

    const BOOKING_STATUES = [
        // self::BOOKING_STATUS_SCHEDULED => "Scheduled",
        self::BOOKING_STATUS_BOOKED => "Booked",
        self::BOOKING_STATUS_CHECKED_IN => "Checked In",
        self::BOOKING_STATUS_CHECKED_OUT => "Checked Out",
        self::BOOKING_STATUS_COMPLETED => "Completed",
    ];

    const BOOKING_PAYMENT_STATUSES = [
        self::PAYMENT_PAID => "Paid",
        self::PAYMENT_PARTIALLY_PAID => "Partially Paid",
        self::PAYMENT_FAILED => "Failed",
        self::PAYMENT_UNPAID => "Unpaid",
    ];


    const DEBIT = 1;
    const CREDIT = 0;
    const TRANSACTION_TYPE_EARNINGS = "earnings";
    const TRANSACTION_TYPE_DEPOSIT = "deposit";
    const TRANSACTION_TYPE_WITHDRAWAL = "withdrawal";
    const TRANSACTION_TYPE_WITHDRAWAL_CANCELLED = "withdrawal cancelled";

    const PHP_DATE_FORMAT = "Y-m-d H:i:s";

    const SERVICE_FEE = 5;
    const TAX_PERCENT = 0.13; //0.13 is 13%
    const TAX_PERCENT_LABEL = "HST Tax - 13%"; //0.13 is 13%
    public static function getAllDayTimeSlot($intervalMinutes = 30)
    {
        $slots = ['00:00'];
        $start = date('Y-m-d') . " 00:00:00";
        $end = date('Y-m-d') . " 23:59:59";
        while ($end > $start) {
            $start = strtotime("+" . $intervalMinutes . " minutes", strtotime($start));
            $start = date('Y-m-d H:i:s', $start);
            if ($end > $start) {
                $slots[] = date('H:i', strtotime($start));
            }
        }
        $slots[] = "23:59";
        return $slots;
    }

    public static function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        // Declare an empty array
        $array = [];
        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');
        $realEnd = new \DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new \DateTime($start), $interval, $realEnd);
        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }
        // Return the array elements
        return $array;
    }

    public static function getTimeSlots()
    {
        return [
            '00:00',
            // '00:30',
            '01:00',
            // '01:30',
            '02:00',
            // '02:30',
            '03:00',
            // '03:30',
            '04:00',
            // '04:30',
            '05:00',
            // '05:30',
            '06:00',
            // '06:30',
            '07:00',
            // '07:30',
            '08:00',
            // '08:30',
            '09:00',
            // '09:30',
            '10:00',
            // '10:30',
            '11:00',
            // '11:30',
            '12:00',
            // '12:30',
            '13:00',
            // '13:30',
            '14:00',
            // '14:30',
            '15:00',
            // '15:30',
            '16:00',
            // '16:30',
            '17:00',
            // '17:30',
            '18:00',
            // '18:30',
            '19:00',
            // '19:30',
            '20:00',
            // '20:30',
            '21:00',
            // '21:30',
            '22:00',
            // '22:30',
            '23:00',
            // '23:30',
            '23:59',
        ];
    }

    public static function getTwelveHourTimeSlots()
    {
        return [
            '12:00',
            '12:30',
            '01:00',
            '01:30',
            '02:00',
            '02:30',
            '03:00',
            '03:30',
            '04:00',
            '04:30',
            '05:00',
            '05:30',
            '06:00',
            '06:30',
            '07:00',
            '07:30',
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '11:59',
        ];
    }

    public static function getTimeOptions($selectedSlot = null)
    {
        $html = '';
        $timeSlots = self::getTimeSlots();
        foreach ($timeSlots as $timeSlot) {
            $selectAttr = '';
            if ($timeSlot == $selectedSlot) {
                $selectAttr = 'selected="selected"';
            }
            $html .= '<option value="' . $timeSlot . '" ' . $selectAttr . '>' . $timeSlot . '</option>';
        }
        return $html;
    }



}
