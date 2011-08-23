<?php
/**
 * @author Fred <mconyango@gmail.com>
 * @copyright 2010 Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Date  {
     
     /**
      * This function takes care of time zone logic
      * This might not be important for the function {@link Application_Model_Date::relativeTime}
      * since even if we use the timezone where the server runs, the timestamp (or time difference) will be the same.
      * But for functions that may be required to return specific time, it would be important
      * @param String $datestring The date string passed to this function
      * @todo complete the time zone logic
      * @todo add more functions to format date
      */
     private function  formatter($datestring=null) {

          /*
           * initialise the instance of Zend_Date and check if its a valid date. If not valid then throw an exception
           *
           */

          //check whether tha date passed in already a Zend_Date instance

          //@todo check whether the given date is a valid date.

          if(!$datestring instanceof Zend_Date){
               /*
                * create an instance of Zend_Date;
                */
               $dateobj=new Zend_Date($datestring,  Zend_Date::ISO_8601);
          }
          else{
               /*
                * use the object the way it is
                */
               $dateobj=$datestring;
          }

          /*
           * time zone logic.
           * Get the UnixTimeStamp offset of the date object and then add <the offset> to the date object timestamp
           * and then finally add 3 hrs(3*60*60)=>Kenyan time, to it.
           */
         // $date='2010-09-26T19:18:36z';

          $offset=$dateobj->getGmtOffset();
          $offset=$offset+(3*60*60);
          $dateobj=$dateobj->addSecond($offset);
          return $dateobj;
     }

     /**
      * This function calculates the reative time beween now and a given time
      *  If the given time is greater (timestamp) than now then it return how much
      * time to an event. If the tme is less than now then the function returns how much
      * time ago an event occurred.
      * @param string $date the date in UTC format as returned by ASI
      * @return the formated string describing how much time ago or to an event
      * @todo check is the passed string is a valid date.
      */

     public static function relativeTime($date){
          
          //test date in utc:2010-09-29T19:18:36z
               $SEC=1;
               $MIN=60*$SEC;
               $HOUR=60*$MIN;
               $DAY= 24*$HOUR;
               $WEEK=7*$DAY;
               $MONTH=30*$DAY;
               $YEAR=12*$MONTH;
          
              if(!$date instanceof Zend_Date){
               /*
                * create an instance of Zend_Date;
                */
               $date=new Zend_Date($date,  Zend_Date::ISO_8601);

              }
         
            $date= $date->toArray();
            $now=Zend_Date::now();
            $now=$now->toArray();
          if($date['timestamp']<$now['timestamp']){

               //get the difference in seconds

               $time_diff=(int)$now['timestamp']-$date['timestamp'];

               /*
                * if difference is less than a minute then display the seconds
                */
               if($time_diff<$MIN){

                    return $time_diff == 1 ? " seconds ago" : $time_diff . " seconds ago";
                  
               }

               /*
                * display minutes
                */
               else if (($time_diff/$MIN)<60){

                      return floor($time_diff/$MIN) == 1 ? " 1 minute ago" : floor($time_diff/$MIN) . " minutes ago";
                    }

               /*
                * display the number of hours and minutes
                */
               else if(($time_diff/$HOUR)<24){
                   $min=number_format(($time_diff%$HOUR)/$MIN, $decimals=null);
                   return floor($time_diff/$HOUR) == 1 ? " 1 hour, " . $min . " minutes ago" : floor($time_diff/$HOUR) . " hours, " . $min . " minutes ago";
               }

               /*
                * display days and hours ago
                */
               else if($time_diff/$DAY<6){

                    $hr=number_format(($time_diff%$DAY)/$HOUR, $decimals=null);
                     return floor($time_diff/$DAY) == 1 ? " Yesterday " : floor($time_diff/$DAY) . " days, " . $hr . " hours ago";
               }

               /*
                * display weeks and days ago
                */
               else if($time_diff/$WEEK <=4){

                    $days=number_format(($time_diff%$WEEK)/$DAY, $decimals=null);
                    return floor($time_diff/$WEEK)==1?" 1 week, " . $days . " days ago" : floor($time_diff/$WEEK) . " weeks, " . $days . " days ago";

               }

               /*
                * display months and days
                */
               else if($time_diff/$MONTH<12){

                    $days=number_format(($time_diff%$MONTH)/$DAY, $decimals=null);
                    return floor($time_diff/$MONTH)==1?" 1 month, " . $days . " days ago" : floor($time_diff/$MONTH) . " months, " . $days . " days ago";

               }

               /*
                * display years and months ago
                */

               else {

                    $months=number_format(($time_diff%$YEAR)/$MONTH, $decimals=null);
                     return floor($time_diff/$YEAR)==1 ? " 1 year, " . $months . " months ago" : floor($time_diff/$YEAR) . " years, " . $months . " months ago";
               }

          }

          /*
           *  calculate how much time to an event
           */

          else{

                 $time_diff=(int)$date['timestamp']-$now['timestamp'];
               /*
                * for the upcoming events it is only necessary to display day/s,week/s,month/s or year/s to the event
                */

               if(($time_diff/$DAY)<6){
                    echo floor($time_diff/$DAY).' days';
               }
               else if(($time_diff/$MONTH)<1){
                    
                    return  floor($time_diff/$WEEK) == 1 ? "1 week " : floor($time_diff/$WEEK) . " weeks";
               }
               else if(($time_diff/$MONTH)<12){
                     
                    return  floor($time_diff/$MONTH) == 1 ? "1 month " : floor($time_diff/$MONTH) . " months";
               }
               else{

                     return  floor($time_diff/$YEAR) == 1 ? "1 year " : floor($time_diff/$YEAR) . " years";
               }
          }
 }

      /**
       * This function just does what {@link Zend_Date::toString} can do but it just formats a date to string
       * e.g it returns something like: 6 jun 2010. This is helpful for those guys who are new to Zend_Date since Zend_Date::toString
       *  displays something like this 2010-06-06 if <dateformat > is not passed to toString($dateformat=null) function.
       * Either way, guys can still use Zend_Date for more interesting date formats.
       * @param String $date the date to be formatted to string
       * @return String formated date e.g 6 jun 2010
       */
      public static function toString($date){

             $date=self::formatter($date);
             return $date->toString(' dSS MMM,Y');

      }
      /**
       * This function checks if the passed date is earlier than today or the second $date param
       * @param string $date1: The date to compare with the second date. If second param is not passed, the current date is used.
       * @param string $date2: The date to compare with $date1. Can be null
       * @return boolean TRUE if $date1 is earlier than $date2, FALSE otherwise
       * @uses Zend_Date
       */
      public static function isEarlier($date1,$date2=null){
           if($date2==null){
                $date2=new Zend_Date(Zend_Date::ISO_8601);
           }
           else{
                $date2=new Zend_Date($date2,Zend_Date::ISO_8601);
           }
           $date1=new Zend_Date($date1,Zend_Date::ISO_8601);

           if($date1->isEarlier($date2)){
                return TRUE;
           }
           else{
                return FALSE;
           }

      }

}