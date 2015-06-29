<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Data\Exception;
use Nora;


class DoseNotSupport extends Exception 
{
    public function __construct($ds)
    {
        parent::__construct(
            Nora::Message(
                "%sはデータソースとして扱えません See Data/DataSource/*",
                get_class($ds)
            )
        );
    }
}
