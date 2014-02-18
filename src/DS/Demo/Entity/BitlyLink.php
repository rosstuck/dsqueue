<?php

namespace DS\Demo\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * A link shared on the bit.ly URL shortener
 * @author Ross Tuck <me@rosstuck.com>
 */
class BitlyLink
{
    /**
     * @var string
     * @Serializer\SerializedName("u")
     * @Serializer\Type("string")
     */
    protected $url;

    /**
     * @var string
     * @Serializer\SerializedName("a")
     * @Serializer\Type("string")
     */
    protected $userAgent;
} 