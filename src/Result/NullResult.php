<?php

namespace zdb\Result;

use zdb\Exception;

class NullResult extends AbstractResult
{
    /**
     * @inheritdoc
     */
    public function fetchAll($mode = null)
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function fetchColumn()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function fetchRow($mode = null)
    {
        return null;
    }
}
