<?php

declare(strict_types=1);

namespace Honed\Action {

    /**
     * @method $this actions(\Honed\Action\Action ...$actions)
     */
    class ActionGroup {}

    /**
     * @method $this query(\Closure(mixed...):void $query) Set the query closure
     */
    class BulkAction {}

    /**
     * @method $this query(\Closure(mixed...):void $query) Set the query closure
     */
    class PageAction {}
}
