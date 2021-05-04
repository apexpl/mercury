<?php
declare(strict_types = 1);

namespace Apex\Mercury\Email;

use Apex\Mercury\Exceptions\MercuryInvalidConfigException;
use Apex\Debugger\Interfaces\DebuggerInterface;
use redis;


/**
 * SMTP Connections
 */
class SMTPConnections
{

    // Properties
    private array $smtp_aliases = [];
    private array $smtp_servers = [];
    private array $connections = [];
    protected ?redis $redis = null;
    protected ?DebuggerInterface $debugger = null;


    /**
     * Get connection
     */
    public function getConnection()
    {

        // Check for redis
        if (count($this->smtp_aliases) == 0 && $this->redis !== null) { 
            $this->smtp_aliases = $this->redis->lrange('config:mercury.smtp_servers', 0, -1);
        }

        // Check for no servers
        if (count($this->smtp_aliases) == 0) { 
            return null;
        }

        // Get SMTP connection
        while (count($this->smtp_aliases) > 0) { 
            $alias = array_shift($this->smtp_aliases);

            // Check for connection
            if (isset($this->connections[$alias])) { 
                $this->smtp_aliases[] = $alias;
                return $this->connections[$alias];
            }

            // Get server vars
            $vars = $this->smtp_servers[$alias] ?? [];
            if (count($vars) == 0 && !$vars = $this->redis?->hgetall('config:mercury.smtp_servers.' . $alias)) { 
                continue;
            }

            // Connect
            if (!$conn = $this->connect($vars)) { 
                continue;
            }

            // Set connection
            $this->smtp_aliases[] = $alias;        
            $this->connections[$alias] = $conn;

            // Return
            return $conn;
        }

        // Return null, no server
        return null;
    }

    /**
     * Close connections
     */
    public function closeConnections():void
    {

        // Close
        foreach ($this->connections as $alias => $sock) { 
            fclose($sock);
        }
        $this->connections = [];
    }

    /**
     * Connect to SMTP
     */
    private function connect(array $vars)
    {

        // Initialize
        $host = $vars['is_ssl'] == 1 ? 'ssl://' . $vars['host'] : $vars['host'];
        if (!$sock = fsockopen($host, (int) $vars['port'], $errno, $errstr, 3)) { 
                $this->debugger?->add(1, "Unable to connect to SMTP server, $host on port $vars[port]", 'alert');
            return null;
        }

// Check greeting
        $res = fread($sock, 1024);
        if (!str_starts_with($res, '220')) { 
            $this->debugger?->add(1, "SMTP $host - Did not receive valid 220 greeting after connect.  Received; $res", 'alert');
            return null;
        }

        // Say HELO
        $res = $this->write($sock, "EHLO apexpl.io");
        if (!str_starts_with($res, '250')) {
            $this->debugger?->add(1, "SMTP $host - EHLO did not return valid 250 response.  Received: $res", 'alert'); 
            return null;
        }

        // Authenticate
        if ($vars['user'] != '' && $vars['password'] != '') { 


            // Auth login
            $res = $this->write($sock, "AUTH LOGIN");
            if (!str_starts_with($res, '334')) { 
                $this->debugger?->add(1, "SMTP $host - Auth Login did not return valid 334 response.  Received: $res", 'alert');
                return null;
            }

            // Username
            $res = $this->write($sock, base64_encode($vars['user']));
            if (!str_starts_with($res, '334')) { 
                $this->debugger?->add(1, "SMTP $host - Auth Username did not return valid 334 response.  Received: $res", 'alert');
                return null;
            }

            // Password
            $res = $this->write($sock, base64_encode($vars['password']));
            if (!str_starts_with($res, '235')) { 
                $this->debugger?->add(1, "SMTP $host - Auth Username did not return valid 235 response.  Received: $res", 'alert');
                return null;
            }
        }

    // Return
        return $sock;
    }

    /**
     * Add server
     */
    public function addServer(array $vars, string $alias = ''):void
    {

        // Get alias, if blank
        if ($alias == '') { 
            $alias = uniqid();
        }

        // Add server
        $this->smtp_servers[$alias] = self::validateServer($vars);
        $this->smtp_aliases[] = $alias;
    }

    /**
     * Validate server
     */
    public static function validateServer(array $vars):array
    {

        // Check required
        foreach (['host', 'port', 'user', 'password'] as $key) { 
            if (!isset($vars[$key])) { 
                throw new MercuryInvalidConfigException("SMTP server does not contain '$key' variable, which is required.");
            }
        }
        if (!isset($vars['is_ssl'])) { $vars['is_ssl'] = 0; }

        // Return
        return $vars;
    }

    /**
     * Write to socket
     */
    protected function write($sock, string $data, int $chk_status = 0):?string
    {

        // Write data{
        fwrite($sock, "$data\r\n");
        $res = fread($sock, 1024);

        // Check response
        if ($chk_status > 0 && !str_starts_with($res, (string) $chk_status)) { 
            return null;
        }

        // Return
        return $res;
    }

}


