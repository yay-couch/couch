<?php
/**
 * Copyright 2015 Kerem Güneş
 *     <http://qeremy.com>
 *
 * Apache License, Version 2.0
 *     <http://www.apache.org/licenses/LICENSE-2.0>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Couch\Http;

use \Couch\Util\Property;

class Response
    extends Stream
{
    use Property;

    private $statusCode,
            $statusText;

    public function __construct(Agent $agent) {
        $this->type = parent::TYPE_RESPONSE;

        // @tmp
        pre($agent->getResult());

        @list($headers, $body) =
            explode("\r\n\r\n", $agent->getResult(), 2);

        $headers = Agent::parseResponseHeaders($headers);
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        if (isset($headers['_status']['code'], $headers['_status']['text'])) {
            $this->setStatusCode($headers['_status']['code'])
                 ->setStatusText($headers['_status']['text']);
        }

        $this->setBody($body,
            (isset($headers['Content-Type']) &&
                   $headers['Content-Type'] == 'application/json'));
    }

    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }
    public function setStatusText($statusText) {
        $this->statusText = $statusText;
        return $this;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }
    public function getStatusText() {
        return $this->statusText;
    }

    public function setBody($body, $isJson = true) {
        $this->body = $isJson
            ? json_decode($body, true) : $body;
        return $this;
    }
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }
}
