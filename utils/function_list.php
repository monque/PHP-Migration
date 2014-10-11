<?php

class MehtodDescParser
{
    protected static $defaultMethod = array(
        'modifier' => array(),
        'type' => null,
        'name' => null,
        'params' => array(),
    );

    protected static $defaultParam = array(
        'optional' => false,
        'type' => null,
        'name' => null,
        'initializer' => null,
        'reference' => false,
    );

    public function __construct()
    {
		libxml_use_internal_errors(true);
    }

    public function parse($dhtml)
    {
        $dhtml = $this->prepare($dhtml);

		libxml_clear_errors();
        $droot = new SimpleXMLElement($dhtml, LIBXML_NONET);
        if ($droot->attributes()->class != 'methodsynopsis dc-description') {
            throw new Exception('Invalid method description html');
        }
		$errors = libxml_get_errors();

        $method = self::$defaultMethod;
        foreach ($droot as $element) {
            $class = $element->attributes()->class;
            $text = strip_tags($element->asXML());

            if ($class == 'modifier') {
                $method['modifier'][$text] = $text;
            } elseif ($class == 'type') {
                $method['type'] = $text;
            } elseif ($class == 'methodname') {
                $method['name'] = $text;
            } elseif ($class == 'methodparam') {
                if ($element != 'void') {
                    $param = $this->parseParam($element);
                    $method['params'][] = $param;
                }
            } else {
                throw new Exception('Unknown method defination class <'.$class.'>');
            }
        }

        return $method;
    }

    protected function prepare($dhtml)
    {
        // Add CDATA to reference param
        $dhtml = preg_replace('/<code([^>]+)>(.*?)<\/code>/s', '<code$1><![CDATA[$2]]></code>', $dhtml);
        return $dhtml;
    }

    protected function parseParam($droot)
    {
        $param = self::$defaultParam;
        foreach ($droot as $element) {
            $class = $element->attributes()->class;

            $xml = $element->asXML();
            if ($element->getName() == 'code') {  // Strip CDATA
                $xml = preg_replace('/<!\[CDATA\[(.*)\]\]>/', '$1', $xml);
            }
            $text = strip_tags($xml);

            if ($class == 'type') {
                $param['type'] = $text;
            } elseif ($class == 'parameter') {
                $param['name'] = $text;
            } elseif ($class == 'parameter reference') {
                $param['name'] = $text;
                $param['reference'] = true;
            } elseif ($class == 'initializer') {
                $param['optional'] = true;  // TODO: 有没有默认值和optional无关
                $param['initializer'] = $text;
            } else {
                throw new Exception('Unknown param defination class <'.$class.'>');
            }
        }

        return $param;
    }
}

// Download from http://php.net/get/php_manual_en.html.gz/from/a/mirror
$html = file_get_contents('php_manual_en.html');

$parser = new MehtodDescParser();

$buildins = array();

preg_match_all('/<div class="methodsynopsis dc-description">.+?<\/div>/s', $html, $matches);
foreach ($matches[0] as $desc) {
    try {
        $method = $parser->parse($desc);
    } catch (Exception $e) {
        continue;
    }

    // Skip class method
    if ($method['modifier']) {
        continue;
    }

    // Find by-reference param
    $posbit = 0;
    foreach ($method['params'] as $key => $param) {
        if ($param['reference']) {
            $posbit |= 1 << $key;
        }
    }
    if (!$posbit) {
        continue;
    }

    // Output
    printf("%s %d\n", $method['name'], $posbit);
}
