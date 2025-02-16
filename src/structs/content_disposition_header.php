<?php
/**
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
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version //autogentag//
 * @filesource
 * @package Mail
 */

/**
 * A container to store a Content-Disposition header as described in http://www.faqs.org/rfcs/rfc2183.
 *
 * This container is used on the contentDisposition property on mail parts.
 * Use it for reading and setting the Content-Disposition header.
 *
 * @package Mail
 * @version //autogentag//
 */
class ezcMailContentDispositionHeader extends ezcBaseStruct
{
    /**
     * Holds language and characterset data for the additional parameters.
     *
     * Format: array(parameterName=>array('charSet'=>string,'language'=>string))
     *
     * @apichange Merge this with $additionalParamters OR come up with an entirely new idea for the ContentDispositionHeader
     * @var array(string=>array())
     */
    public $additionalParametersMetaData = [];

    /**
     * Constructs a new ezcMailContentDispositionHeader holding the various values of this
     * container.
     *
     * @param string $disposition
     * @param string $fileName
     * @param string $creationDate
     * @param string $modificationDate
     * @param string $readDate
     * @param string $size
     * @param array(string=>string) $additionalParameters
     * @param string $fileNameLanguage
     * @param string $fileNameCharSet
     */
    public function __construct(
        /**
         * The disposition type, either "inline" or "attachment".
         *
         */
        public $disposition = 'inline',
        /**
         * The filename of the attachment.
         *
         * The filename should never include path information.
         *
         */
        public $fileName = null,
        /**
         * The creation date of the file attachment.
         *
         * The time should be formatted as specified by http://www.faqs.org/rfcs/rfc822.html
         * section 5.
         *
         * A typical example is: Sun, 21 May 2006 16:00:50 +0400
         *
         */
        public $creationDate = null,
        /**
         * The last modification date of the file attachment.
         *
         * The time should be formatted as specified by http://www.faqs.org/rfcs/rfc822.html
         * section 5.
         *
         * A typical example is: Sun, 21 May 2006 16:00:50 +0400
         *
         */
        public $modificationDate = null,
        /**
         * The last date the file attachment was read.
         *
         * The time should be formatted as specified by http://www.faqs.org/rfcs/rfc822.html
         * section 5.
         *
         * A typical example is: Sun, 21 May 2006 16:00:50 +0400
         *
         */
        public $readDate = null,
        /**
         * The size of the content in bytes.
         *
         */
        public $size = null,
        /**
         * Any additional parameters provided in the Content-Disposition header.
         *
         * The format of the field is array(parameterName=>parameterValue)
         *
         * @var array(string=>string)
         */
        public $additionalParameters = [],
        /**
         * The language of the filename.
         *
         */
        public $fileNameLanguage = null,
        /**
         * The characterset of the file name.
         *
         */
        public $fileNameCharSet = null,
        /**
         * The filename of the attachment, formatted for display. Used only for
         * parsing, not used when generating a mail.
         *
         * The filename should never include path information.
         *
         * Added for issue #13038. If you use __set_state() be sure to set this
         * property also.
         *
         */
        public $displayFileName = null
    )
    {
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMailAddress
     */
    static public function __set_state( array $array )
    {
        return new ezcMailContentDispositionHeader( $array['disposition'],
                                                    $array['fileName'],
                                                    $array['creationDate'],
                                                    $array['modificationDate'],
                                                    $array['readDate'],
                                                    $array['size'],
                                                    $array['additionalParameters'],
                                                    $array['fileNameLanguage'],
                                                    $array['fileNameCharSet'],
                                                    $array['displayFileName']
                                                    );
    }
}

?>
