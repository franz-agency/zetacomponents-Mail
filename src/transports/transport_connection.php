<?php
/**
 * File containing the ezcMailTransportConnection class
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package Mail
 * @version //autogen//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @access private
 */

/**
 * ezcMailTransportConnection is an internal class used to connect to
 * a server and have line based communication with.
 *
 * @property ezcMailTransportOptions $options
 *           Holds the options you can set to the transport connection.
 *
 * @package Mail
 * @version //autogen//
 * @access private
 */
class ezcMailTransportConnection
{
    /**
     * The line-break characters to send to the server.
     */
    final public const CRLF = "\r\n";

    /**
     * The connection to the server or null if there is none.
     *
     * @var resource
     */
    private $connection = null;

    /**
     * Options for a transport connection.
     *
     */
    private \ezcMailTransportOptions $options;

    /**
     * Constructs a new connection to the $server using the port $port.
     *
     * {@link ezcMailTransportOptions} for options you can specify for a
     * transport connection.
     *
     * @todo The @ should be removed when PHP doesn't throw warnings for connect problems.
     *
     * @throws ezcMailTransportException
     *         if a connection to the server could not be made
     * @throws ezcBaseExtensionNotFoundException
     *         if trying to use SSL and the extension openssl is not installed
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param string $server
     * @param int $port
     */
    public function __construct( $server, $port, ezcMailTransportOptions $options = null )
    {
        $errno = null;
        $errstr = null;
        if ( $options === null )
        {
            $this->options = new ezcMailTransportOptions();
        }
        else
        {
            $this->options = $options;
        }
        if ( $this->options->ssl )
        {
            if ( ezcBaseFeatures::hasExtensionSupport( 'openssl' ) !== true )
            {
                throw new ezcBaseExtensionNotFoundException( 'openssl', null, "PHP not configured --with-openssl." );
            }
            $this->connection = @stream_socket_client( "ssl://{$server}:{$port}",
                                                       $errno, $errstr, $this->options->timeout );
        }
        else
        {
            $this->connection = @stream_socket_client( "tcp://{$server}:{$port}",
                                                       $errno, $errstr, $this->options->timeout );
        }

        if ( is_resource( $this->connection ) )
        {
            stream_set_timeout( $this->connection, $this->options->timeout );
        }
        else
        {
            throw new ezcMailTransportException( "Failed to connect to the server: {$server}:{$port}." );
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not accepted for the property $name
     * @param string $name
     * @ignore
     */
    public function __set( $name, mixed $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( !( $value instanceof ezcMailTransportOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcMailTransportOptions' );
                }
                $this->options = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        return match ($name) {
            'options' => $this->options,
            default => throw new ezcBasePropertyNotFoundException( $name ),
        };
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return match ($name) {
            'options' => true,
            default => false,
        };
    }

    /**
     * Send $data to the server through the connection.
     *
     * This method appends one line-break at the end of $data.
     *
     * @throws ezcMailTransportException
     *         if there is no valid connection.
     * @param string $data
     */
    public function sendData( $data )
    {
        if ( is_resource( $this->connection ) )
        {
            if ( fwrite( $this->connection, $data . self::CRLF,
                        strlen( $data ) + strlen( self::CRLF  ) ) === false )
            {
                throw new ezcMailTransportException( 'Could not write to the stream. It was probably terminated by the host.' );
            }
        }
    }

    /**
     * Returns one line of data from the stream.
     *
     * The returned line will have linebreaks removed if the $trim option is set.
     *
     * @throws ezcMailTransportConnection
     *         if there is no valid connection
     * @param bool $trim
     * @return string
     */
    public function getLine( $trim = false )
    {
        $data = '';
        $line = '';

        if ( is_resource( $this->connection ) )
        {
            // in case there is a problem with the connection fgets() returns false
            while ( !str_contains( $data, self::CRLF ) )
            {
                $line = fgets( $this->connection, 512 );

                /* If the mail server aborts the connection, fgets() will
                 * return false. We need to throw an exception here to prevent
                 * the calling code from looping indefinitely. */
                if ( $line === false )
                {
                    $this->connection = null;
                    throw new ezcMailTransportException( 'Could not read from the stream. It was probably terminated by the host.' );
                }

                $data .= $line;
            }

            if ( $trim == false )
            {
                return $data;
            }
            else
            {
                return rtrim( $data, "\r\n" );
            }
        }
        throw new ezcMailTransportException( 'Could not read from the stream. It was probably terminated by the host.' );
    }

    /**
     * Returns if the connection is open.
     *
     * @return bool
     */
    public function isConnected()
    {
        return is_resource( $this->connection );
    }

    /**
     * Closes the connection to the server if it is open.
     */
    public function close()
    {
        if ( is_resource( $this->connection ) )
        {
            fclose( $this->connection );
            $this->connection = null;
        }
    }
}
?>
