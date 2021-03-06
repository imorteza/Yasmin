<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved.
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 */

namespace CharlotteDunois\Yasmin\WebSocket\Handlers;

use CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSHandler;
use function mt_rand;

/**
 * WS Event handler.
 *
 * @internal
 */
class InvalidSession implements WSHandlerInterface
{
    protected $wshandler;

    public function __construct(WSHandler $wshandler)
    {
        $this->wshandler = $wshandler;
    }

    public function handle(WSConnection $ws, $data): void
    {
        if (! $data['d']) {
            $ws->setSessionID(null);
        }

        $this->wshandler->wsmanager->client->addTimer(
            mt_rand(1, 5),
            function () use (&$ws) {
                $ws->sendIdentify();
            }
        );
    }
}
