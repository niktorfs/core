<?php

namespace tdt\core\datasets;

/**
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */

/**
 * This class is the internal datatank object.
 * It contains properties that add information about where
 * the data comes from, paging information and geo information.
 */
class Data {

    public $definition;

    public $paging;

    public $geo;

    public $data;
}