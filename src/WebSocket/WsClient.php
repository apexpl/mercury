<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket;

/**
 * Websocket client
 */
class WsClient
{

    /**
     * Constructor
     */
    public function __construct(
        private string $admin_pass, 
        private string $host = '127.0.0.1', 
        private int $port = 4863
    ) { 

    }

    /**
     * Send message
     */
    public function send(array $data):bool
    {

        // Get auth
        $auth = [
            'action' => 'authenticate', 
            'token' => 'admin:' . $this->admin_pass
        ];
        array_unshift($data, $auth);

        // Dispatch
        return $this->dispatch(json_encode($data));
    }

    /**
     * Send
     */
    private function dispatch(string $message):bool
    {

        // Connect
        if (!$sock = @fsockopen('tcp://' . $this->host, $this->port, $errno, $errstr, 5)) { 
            return false;
        }
        stream_set_timeout($sock, 5);

        // Send headers
        fwrite($sock, "GET / HTTP/1.1\r\n");
        fwrite($sock, "Host: " . $this->host . ':' . $this->port . "\r\n");
        fwrite($sock, "user-agent: websocket-client-php\r\n");
        fwrite($sock, "connection: Upgrade\r\n");
        fwrite($sock, "upgrade: websocket\r\n");
        fwrite($sock, "sec-websocket-key: " . $this->generateWsKey() . "\r\n");
        fwrite($sock, "sec-websocket-version: 13\r\n");
        fwrite($sock, "\r\n");

        // Get response
        $response = fread($sock, 1024);

        $metadata = stream_get_meta_data($sock);

        // Send the message
        $message = $this->formatMessage($message);
        fwrite($sock, $message);

        // Close and return
        fclose($sock);
        return true;
    }

    /**
     * Generate key
     */
    private function generateWsKey():string
    {
        $string = unpack("H*", random_bytes(8))[1];
        return base64_encode($string);
    }

    /**
     * Format message
     */
    private function formatMessage($payload, $opcode = 'text', $masked = true):string
    {

        // Write FIN, final fragment bit.
        $frame_head_binstr = '';
        $final = true; /// @todo Support HUGE payloads.
        $frame_head_binstr .= $final ? '1' : '0';

        // RSV 1, 2, & 3 false and unused.
        $frame_head_binstr .= '000';

        // Opcode rest of the byte.
        $frame_head_binstr .= sprintf('%04b', 1);

        // Use masking?
        $frame_head_binstr .= $masked ? '1' : '0';

        // 7 bits of payload length...
        $payload_length = strlen($payload);
        if ($payload_length > 65535) {
            $frame_head_binstr .= decbin(127);
            $frame_head_binstr .= sprintf('%064b', $payload_length);
        }
        elseif ($payload_length > 125) {
            $frame_head_binstr .= decbin(126);
            $frame_head_binstr .= sprintf('%016b', $payload_length);
        }
        else {
            $frame_head_binstr .= sprintf('%07b', $payload_length);
        }
        $frame = '';

        // Write frame head to frame.
        foreach (str_split($frame_head_binstr, 8) as $binstr) $frame .= chr(bindec($binstr));

        // Handle masking
        if ($masked) {
            // generate a random mask:
            $mask = '';
            for ($i = 0; $i < 4; $i++) $mask .= chr(rand(0, 255));
            $frame .= $mask;
        }

        // Append payload to frame:
        for ($i = 0; $i < $payload_length; $i++) {
            $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
        }

        // Return
        return $frame;
    }

}


