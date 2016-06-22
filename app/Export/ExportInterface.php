<?php

namespace App\Export;

Interface ExportInterface {

    /**
     * Get human name of this export
     */
    static function getHumanName();

    /**
     * Get the file of the format
     */
    static function exportFile($analyses);

}
