<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 14/09/2018
 * Time: 8:51 PM
 */

namespace app\chiia\validate;

use think\Validate;

class Spider extends Validate{
    protected $rule = [
        'source' => 'require',
        'startDate' => 'require',
        'endDate' => 'require|checkDate:',
        'term' => 'require',
    ];

    protected $message = [
        'endDate.checkDate' => 'Failed: Searching endDate must later than startDate',
        'term'=> 'Failed: Searching terms cannot be null',
    ];

    protected function checkDate($value, $rule, $data){
        if ($value < $data['startDate']){
            return false;
        }
        else{
            return true;
        }

    }
}