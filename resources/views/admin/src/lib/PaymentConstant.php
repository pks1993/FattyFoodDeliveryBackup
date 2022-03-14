<?php
/**
 * Created by PhpStorm.
 * User: l00428552
 * Date: 2019/7/30
 * Time: 14:19
 */

class PaymentConstant
{
    const API_METHOD_PREFIX = "kbz.payment";

    const API_METHOD_QUERY_ORDER = self::API_METHOD_PREFIX . ".queryorder";

    const API_METHOD_PRE_CREATE = self::API_METHOD_PREFIX . ".precreate";

    const API_METHOD_REFUND = self::API_METHOD_PREFIX . ".refund";
}