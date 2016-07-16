<?php

namespace zdb\Adapter;

use zdb\Adapter;
use zdb\Query;
use zdb\Exception;
use zdb\Result\NullResult;

class NullAdapter extends AbstractAdapter
{
    /**
     * @inheritdoc
     */
    public function query($query)
    {
        // Log query
        if( $this->logger ) {
            if( $query instanceof Query ) {
                $queryString = $query->toString();
            } else {
                $queryString = (string) $query;
            }
            $this->logger->debug($queryString);
        }

        if( $query instanceof Query\SelectQuery ) {
            return new NullResult();
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function quote($string)
    {
        throw new Exception\RuntimeException('Not implemented');
    }
}
